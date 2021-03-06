<template>
    <div>
        <div v-if="warning" class="uk-alert-warning" uk-alert>{{ warning }}</div>
        <input type="hidden" :name="name + '[domain]'" :value="domain" />
        <input type="hidden" :name="name + '[content_type]'" :value="contentType" />
        <input type="hidden" :name="name + '[content]'" :value="content" />

        <div class="uk-modal-container" :id="modalId" uk-modal>
            <div class="uk-modal-dialog" uk-overflow-auto>
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div v-html="modalHtml"></div>
            </div>
        </div>

        <button v-if="!content" class="uk-placeholder" v-on:click.prevent="openModal">
            <span v-html="feather.icons['plus'].toSvg({ width: 18, height: 18 })"></span>
            Select...
        </button>
        <div v-if="content" class="content-holder">
            <div v-if="loading" uk-spinner></div>
            <div  v-if="!loading">
                <div class="meta">{{ contentType }} /</div>
                <img :src="image">
                {{ title }}
            </div>
            <button class="close-button" v-html="feather.icons['x'].toSvg({ width: 20, height: 20 })" v-on:click.prevent="clearSelection"></button>
        </div>
    </div>
</template>

<script>
    import { GraphQLClient } from 'graphql-request'
    import UIkit from 'uikit';
    import feather from 'feather-icons';

    export default {
        data() {
            var value = JSON.parse(this.value);

            // Find out all content label fields that we need to get form the API.
            var contentLabelFieldRegEx = /{([0-9a-z_]+)}/g;
            var matches, contentLabelFields = [];
            while (matches = contentLabelFieldRegEx.exec(this.contentLabel)) {
                contentLabelFields.push(matches[1]);
            }

            return {
                modal: null,
                domain: value.domain,
                contentType: value.content_type,
                content: value.content ? value.content : null,
                contentLabelFields: contentLabelFields,
                loading: false,
                title: '',
                feather: feather,
                warning: null,
                image: null
            };
        },
        props: [
            'name',
            'value',
            'modalHtml',
            'apiUrl',
            'contentLabel',
            'previewImage'
        ],
        created() {

            this.client = new GraphQLClient(this.apiUrl, {
                credentials: "same-origin",
                headers: {
                    "Authentication-Fallback": true
                },
            });

            // Load by getting the content object from the API.
            if(this.content) {
                this.findHumanReadableName();
            }
        },
        mounted() {
            let $modalEl = this.$el.querySelector('#' + this.modalId);
            this.modal = UIkit.modal($modalEl);

            // When closing a modal, we stop listening to contentSelected events.
            UIkit.util.on($modalEl, 'beforehide', () => {
                window.UniteCMSEventBus.$off('contentSelected');
            });
        },
        computed: {
            modalId() {
                return 'modal_' + this._uid;
            }
        },
        methods: {
            openModal() {

                // When opening a modal, we start listen to contentSelected events.
                window.UniteCMSEventBus.$on('contentSelected', (data) => {
                    // For the moment, we can only handle single selections
                    if(data.length > 0) {
                        this.content = data[0].row.id;
                        this.contentType = data[0].contentType;
                        this.findHumanReadableName();
                        this.modal.hide();
                    }
                });

                this.modal.show();
            },
            clearSelection() {
                this.content = null;
                this.title = null;
                this.warning = null;
            },
            findHumanReadableName() {
                if(this.content) {
                    this.loading = true;
                    let schemaType = this.contentType.charAt(0).toUpperCase() + this.contentType.slice(1);
                    let queryMethod = 'get' + schemaType;
                    let label = this.contentLabel;
                    let imageField = `${this.previewImage} {url}`;
                    const fields = [
                        ...this.contentLabelFields,
                        imageField
                    ];

                    this.client.request(`
                            query($id : ID!) {` + queryMethod + `(id: $id) {
                                ` + fields.join(',') + `
                            }
                        }`, { 'id': this.content }).then((data) => {

                        this.contentLabelFields.forEach((field) => {
                            label = label.replace('{' + field + '}', data[queryMethod][field]);
                        });
                        this.selected = data
                        this.image = data[queryMethod][this.previewImage]['url']
                        this.title = label;
                        this.loading = false;
                    }).catch(() => {
                        this.title = '#' + this.content;
                        this.loading = false;
                        this.warning = 'This content label "'+label+'" could not be resolved. Please make sure, that all field placeholders exist.'
                    });
                }
            }
        }
    };
</script>

<style lang="scss">
    karls-reference-image-field {
        position: relative;
        display: block;
        padding: 5px 0;

        img {
            max-height: 85px;
        }

        button.uk-placeholder {
            padding: 10px;
            height: 46px;
            display: block;
            width: 100%;
            border-color: #989898;
            color: #989898;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 2px;
            margin: 0;

            svg.feather {
                margin-top: -3px;
            }

            &:hover {
                border-color: #242424;
                color: #242424;
            }
        }

        .content-holder {
            position: relative;
            background: white;
            border: 1px solid #d8d8d8;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.06);
            padding: 10px;
            border-radius: 2px;

            .uk-spinner {
                svg {
                    width: 20px;
                    height: 20px;
                }
            }

            .close-button {
                right: 5px;
                top: 50%;
                margin-top: -20px;
            }

            &:hover {
                .close-button {
                    color: red;
                }
            }

            .meta {
                font-size: 0.6rem;
                line-height: normal;
                color: #cecece;
                text-transform: uppercase;
            }
        }
    }
</style>
