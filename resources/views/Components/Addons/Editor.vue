<template>
    <div style="border: 1px solid var(--el-border-color-light);border-radius: 4px;">
        <Toolbar style="border-bottom: 1px solid var(--el-border-color-light)" :editor="editor"
            :defaultConfig="toolbarConfig" :mode="mode" />
        <Editor style="height: 460px; overflow-y: hidden;" v-model="value" :defaultConfig="editorConfig"
            @change="onChange" :mode="mode" @onCreated="onCreated" />
    </div>
</template>
<script>
import '@wangeditor/editor/dist/css/style.css'
import { Boot } from '@wangeditor/editor'
import attachmentModule from '@wangeditor/plugin-upload-attachment'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import Cookies from 'js-cookie'
Boot.registerModule(attachmentModule)
export default {
    components: {
        Editor,
        Toolbar
    },
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        mode: {
            type: String,
            default: 'default'
        }
    },
    computed: {
        value: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit("update:modelValue", value);
            }
        }
    },
    data() {
        return {
            editor: null,
            toolbarConfig: {
                insertKeys: {
                    index: 0,
                    keys: ['uploadAttachment'],
                },
            },
            editorConfig: {
                placeholder: this.placeholder,
                customAlert: (s, t) => {
                    switch (t) {
                        case 'success':
                            this.$message.success(s)
                            break
                        case 'info':
                            this.$message.info(s)
                            break
                        case 'warning':
                            this.$message.warning(s)
                            break
                        case 'error':
                            this.$message.error(s)
                            break
                        default:
                            this.$message.info(s)
                            break
                    }
                },
                hoverbarKeys: {
                    attachment: {
                        menuKeys: ['downloadAttachment'],
                    },
                },
                MENU_CONF: {
                    uploadImage: {
                        server: this.$route('uploadImage'),
                        fieldName: 'file',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                        },
                        onSuccess: (file, res) => {
                            this.$emit('onImageUpload', res.data)
                        }
                    },

                    uploadVideo: {
                        server: this.$route('uploadVideo'),
                        fieldName: 'file',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                        },
                        onSuccess: (file, res) => {
                            this.$emit('onVideoUpload', res.data)
                        }
                    },
                    uploadAttachment: {
                        server: this.$route('uploadFile'),
                        fieldName: 'file',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                        },
                        onSuccess: (file, res) => {
                            this.$emit('onFileUpload', res.data)
                        }
                    },
                }

            },
        }
    },
    beforeDestroy() {
        const editor = this.editor
        if (editor == null) return
        editor.destroy()
    },
    methods: {
        onCreated(editor) {
            this.editor = Object.seal(editor)
        },
        onChange(editor) { this.$emit('onChange', editor.children) },
    }
}
</script>
<style scoped></style>