<template>
    <div class="search-overlay" :class="{'search-overlay--visible': visible}">
        <span class="search-overlay__close-button" title="Close" @click="$emit('onClose')">
            &#10005;
        </span>

        <div class="search-overlay__container">
            <form class="form" @submit.prevent>
                <div class="form__group">
                    <input type="search" class="form__input" placeholder="Search for user" ref="search"
                           @input='onInput($event.target.value)'>
                    <font-awesome-icon :icon="['fas', 'search']"></font-awesome-icon>
                </div>
            </form>

            <div class="search-overlay__results" v-if="searchQuery">
                <div class="search-overlay__results-inner">
                    <i class="loader" v-if="isSearching"></i>

                    <ul class="search-results">
                        <li class="search-results__item" v-for="result in results" :key="result.id">
                            <div class="search-results__avatar">
                                <img src="https://via.placeholder.com/500" alt="User Avatar">
                            </div>
                            <div class="search-results__name">
                                {{ fullName(result) }}
                            </div>
                            <div class="search-results__action">
                                <button @click="startConversationWithUser(result)">
                                    <font-awesome-icon :icon="['fas', 'comment-dots']"></font-awesome-icon>
                                </button>
                            </div>
                        </li>
                    </ul>

                    <infinite-loading @infinite="infiniteLoadingHandler" ref="infiniteLoading" v-if="!isSearching">
                        <template #no-more>
                            <span></span>
                        </template>
                        <template #no-results>
                            No results found
                            <font-awesome-icon :icon="['far', 'frown']"></font-awesome-icon>
                        </template>
                    </infinite-loading>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ApiRoutes from "../../../api/routes";
import { debounce } from 'lodash'
import { mapActions, mapGetters } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import InfiniteLoading from 'vue-infinite-loading'

export default {
    name: "ContactSearchOverlay",

    components: {
        FontAwesomeIcon,
        InfiniteLoading
    },

    props: {
        visible: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            searchQuery: '',
            results: [],
            currentPage: 1,
            lastPage: null,
            isSearching: false
        }
    },

    computed: {
        ...mapGetters({
            getContactById: 'contacts/contactById'
        })
    },

    methods: {
        ...mapActions({
            selectContact: 'contacts/selectContact',
            addNewContact: 'contacts/addNewContact'
        }),

        onInput(value) {
            this.isSearching = true
            this.setSearchQuery(value)
        },

        setSearchQuery: debounce(function (searchQuery) {
            this.searchQuery = searchQuery
        }, 600),

        fullName: (user) => `${user.first_name} ${user.last_name}`,

        startConversationWithUser(user) {
            if (!this.getContactById(user.id)) {
                this.addNewContact(user)
            }
            this.selectContact(user.id)
            this.$emit('onClose')
        },

        async infiniteLoadingHandler($state) {
            if (this.searchQuery === '' || (this.lastPage !== null && this.currentPage > this.lastPage)) {
                $state.complete()
                return
            }

            const response = await axios.get(ApiRoutes.Users.Search(this.searchQuery.trim(), this.currentPage))
            if (response.data.data.length) {
                this.currentPage++
                this.results.push(...response.data.data)
                this.lastPage = response.data.meta.last_page
                $state.loaded()
            } else {
                $state.complete()
            }
        }
    },

    watch: {
        searchQuery() {
            this.results = []
            this.currentPage = 1
            this.isSearching = false
            this.$refs.infiniteLoading?.stateChanger.reset()
        },

        visible() {
            if (this.visible) {
                this.$nextTick(() => this.$refs.search.focus())
            } else {
                this.searchQuery = null
                this.results = []
                this.$refs.search.value = ''
            }
        }
    }
}
</script>
