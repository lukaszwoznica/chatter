<template>
    <div class="search-overlay" :class="{'search-overlay--visible': visible}">
        <span class="search-overlay__close-button" title="Close" @click="$emit('onClose')">
            &#10005;
        </span>
        <div class="search-overlay__container">
            <form class="form" @submit.prevent>
                <div class="form__group">
                    <input type="search" class="form__input" placeholder="Search for user" ref="search" @input='onInput($event.target.value)'>
                    <font-awesome-icon :icon="['fas', 'search']"></font-awesome-icon>
                </div>
            </form>

            <p v-show="isSearching">
                Searching...
            </p>

            <div class="search-overlay__results" v-if="results.length">
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
            </div>

            <p v-else-if="searchQuery && !isSearching">No results</p>
        </div>
    </div>
</template>

<script>
import ApiRoutes from "../../../api/routes";
import { debounce } from 'lodash'
import { mapActions, mapGetters } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

export default {
    name: "ContactSearchOverlay",

    components: {
      FontAwesomeIcon
    },

    props: {
        visible: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            searchQuery: null,
            results: [],
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
        }
    },

    watch: {
        async searchQuery() {
            if (!this.searchQuery) {
                this.results = []
                this.isSearching = false
                return
            }

            const response = await axios.get(ApiRoutes.Users.Search(this.searchQuery.trim()))

            this.results = response.data.data
            this.isSearching = false
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
