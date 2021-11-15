<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sopamo\LaravelFilepond\Filepond;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilepondTest extends TestCase
{
    use RefreshDatabase;

    private User $currentUser;
    private UploadedFile $file;
    private Filepond $filepond;
    private string $filepondDisk;
    private string $filepondPath;
    private string $filepondInput;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()->create();
        $this->file = UploadedFile::fake()->image('image.jpg');
        $this->filepond = App::make('Sopamo\LaravelFilepond\Filepond');
        $this->filepondDisk = config('filepond.temporary_files_disk');
        $this->filepondPath = config('filepond.temporary_files_path');
    }

    public function testUserCanUploadFile()
    {
        Storage::fake($this->filepondDisk);

        $response = $this->actingAs($this->currentUser)
            ->postJson(route('filepond.upload'), [
                'file' => $this->file
            ]);

        $absoluteFilePath = $this->filepond->getPathFromServerId($response->content());
        $storageFilePath = strstr($absoluteFilePath, $this->filepondPath);

        $response->assertOk();
        Storage::disk($this->filepondDisk)->assertExists($storageFilePath);
    }

    public function testUserCannotUploadFileWhenUnauthenticated()
    {
        $response = $this->postJson(route('filepond.upload'), [
            config('filepond.input_name') => $this->file
        ]);

        $response->assertUnauthorized();
    }

    public function testUserMustProvideFileToUpload()
    {
        $response = $this->actingAs($this->currentUser)
            ->postJson(route('filepond.upload'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserCanDeleteUploadedFile()
    {
        Storage::fake($this->filepondDisk);

        $uploadedFile = $this->file->storeAs(
            $this->filepondPath . '/' . Str::random(),
            $this->file->getClientOriginalName(),
            $this->filepondDisk
        );
        $uploadedFilePath = Storage::disk($this->filepondDisk)->path($uploadedFile);
        $serverId = $this->filepond->getServerIdFromPath($uploadedFilePath);

        $response = $this->actingAs($this->currentUser)
            ->call(method: 'DELETE', uri: route('filepond.delete'), content: $serverId);

        $response->assertNoContent();
        Storage::disk($this->filepondDisk)->assertMissing($uploadedFile);
    }

    public function testUserCannotDeleteUploadedFileWhenUnauthenticated()
    {
        $response = $this->deleteJson(route('filepond.delete', [Str::random()]));

        $response->assertUnauthorized();
    }
}
