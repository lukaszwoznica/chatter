<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    private User $currentUser;
    private UploadedFile $avatarFile;
    private string $filepondDisk;
    private string $mediaLibraryDisk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()->create();
        $this->avatarFile = UploadedFile::fake()->image('avatar.png');
        $this->filepondDisk = config('filepond.temporary_files_disk');
        $this->mediaLibraryDisk = config('media-library.disk_name');
    }

    public function testUserCanUploadAvatarImage()
    {
        Storage::fake($this->mediaLibraryDisk);

        $uploadedAvatarData = $this->simulateAvatarUploadWithFilepond();

        $response = $this->actingAs($this->currentUser)
            ->postJson(route('users.avatar.store'), [
                'avatar_server_id' => $uploadedAvatarData['avatar_server_id']
            ]);

        $avatarUrl = $response->json('data.avatar_url');
        $avatarThumbUrl = $response->json('data.avatar_thumb_url');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'avatar_url',
                    'avatar_thumb_url'
                ]
            ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $this->currentUser->id,
            'collection_name' => 'avatar',
            'file_name' => basename($avatarUrl)
        ]);
        Storage::disk($this->mediaLibraryDisk)
            ->assertExists([
                strstr($avatarUrl, config('media-library.prefix')),
                strstr($avatarThumbUrl, config('media-library.prefix'))
            ]);
        Storage::disk($this->filepondDisk)
            ->assertMissing($uploadedAvatarData['temp_avatar_path']);
    }

    public function testUserCannotUploadAvatarImageWhenUnauthenticated()
    {
        $uploadedAvatarData = $this->simulateAvatarUploadWithFilepond();

        $response = $this->postJson(route('users.avatar.store'), [
            'avatar_server_id' => $uploadedAvatarData['avatar_server_id']
        ]);

        $response->assertUnauthorized();
    }

    public function testAvatarServerIdIsRequiredToUploadAvatarImage()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('users.avatar.store'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('avatar_server_id');
    }

    public function testUserMustProvideValidAvatarServerIdToUploadAvatarImage()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('users.avatar.store'), [
                'avatar_server_id' => Str::random()
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('avatar_server_id');
    }

    public function testUserCanDeleteHisAvatar()
    {
        Storage::fake($this->mediaLibraryDisk);
        $userService = App::make('App\Services\UserService');

        $uploadedAvatarData = $this->simulateAvatarUploadWithFilepond();
        $avatarAbsolutePath = Storage::disk($this->filepondDisk)->path($uploadedAvatarData['temp_avatar_path']);
        $userAvatar = $userService->uploadUserAvatar($this->currentUser, $avatarAbsolutePath);

        $response = $this->actingAs($this->currentUser)
            ->deleteJson(route('users.avatar.destroy'));

        $response->assertNoContent();
        $this->assertDatabaseMissing('media', [
            'model_id' => $this->currentUser->id,
            'collection_name' => 'avatar',
            'file_name' => $userAvatar->file_name
        ]);
        Storage::disk($this->mediaLibraryDisk)->assertMissing([
            strstr($userAvatar->getUrl(), config('media-library.prefix')),
            strstr($userAvatar->getUrl('thumb'), config('media-library.prefix'))
        ]);
    }

    public function testUserCannotDeleteHisAvatarWhenUnauthenticated()
    {
        $response = $this->deleteJson(route('users.avatar.destroy'));

        $response->assertUnauthorized();
    }

    private function simulateAvatarUploadWithFilepond(): array
    {
        Storage::fake($this->filepondDisk);
        $filepond = App::make('Sopamo\LaravelFilepond\Filepond');

        $uploadedAvatar = $this->avatarFile->storeAs(
            config('filepond.temporary_files_path') . '/' . Str::random(),
            $this->avatarFile->getClientOriginalName(),
            $this->filepondDisk
        );
        $avatarAbsolutePath = Storage::disk($this->filepondDisk)->path($uploadedAvatar);

        return [
            'temp_avatar_path' => $uploadedAvatar,
            'avatar_server_id' => $filepond->getServerIdFromPath($avatarAbsolutePath)
        ];
    }
}
