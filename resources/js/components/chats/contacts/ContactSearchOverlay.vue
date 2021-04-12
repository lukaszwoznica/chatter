<template>
    <div class="search-overlay" :class="{'search-overlay--visible': visible}">
        <span class="search-overlay__close-button" title="Close" @click="$emit('onClose')">
            x
        </span>
        <div class="search-overlay__container">
            <form @submit.prevent>
                <input type="text" placeholder="Search..."
                       ref="search"
                       @input='onInput($event.target.value)'>
            </form>
            <p v-show="isSearching">
                Searching...
            </p>
            <ul class="results__list" v-if="results.length">
                <li class="results__item" v-for="result in results" :key="result.id">
                    {{ fullName(result) }}
                    <AppButton @onClick="startConversation(result)">
                        Start conversation
                    </AppButton>
                </li>
            </ul>
            <p v-else-if="searchQuery && !isSearching">No results</p>
        </div>
    </div>
</template>

<script>
import AppButton from '../../ui/AppButton';
import ApiRoutes from "../../../api/routes";
import {debounce} from 'lodash'
import {mapActions} from 'vuex'

export default {
    name: "ContactSearchOverlay",

    components: {
        AppButton
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

        startConversation(user) {
            this.addNewContact(user)
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

<style scoped>
.search-overlay {
    height: 100%;
    width: 100%;
    display: none;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.8);
}

.search-overlay--visible {
    display: block;
}

.search-overlay__container {
    position: relative;
    display: flex;
    flex-direction: column;
    text-align: center;
    justify-content: center;
    top: 15%;
    width: 100%;
}

form {
    width: 100%;
    text-align: center;
}

input {
    height: 50px;
    width: 50%;
    opacity: 0.9;
}

.search-overlay__close-button {
    position: absolute;
    top: 20px;
    right: 45px;
    font-size: 60px;
    cursor: pointer;
    color: white;
}

p, li {
    color: #ffffff;
}
</style>
