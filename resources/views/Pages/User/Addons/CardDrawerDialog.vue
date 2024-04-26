<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="800px" @closed="$emit('closed')">
        <el-row :gutter="20">
            <el-col :span="16">
                <div class="card-drawer">
                    <div class="card-drawer-content">
                        <el-image :src="file" placeholder="图像加载中" fit="fill">
                            <template #error>
                                <div class="card-drawer-content-error" @click="clickUpload">
                                    <el-icon-warning />
                                    <span>请上传模板图片</span>
                                </div>
                            </template>
                        </el-image>
                        <div class="card-drawer-content-resize">
                            <VueDragResize v-for="(rect, index) in design" :key="index" :w="rect.width" :h="rect.height"
                                :x="rect.left" :y="rect.top" :parentW="500" :parentH="400" :minw="60" :minh="20"
                                :isDraggable="true" :isResizable="true" :z="index"
                                v-on:dragging="changeDesign($event, index)" v-on:resizing="changeDesign($event, index)">
                                <div class="filler" :style="{ fontSize: rect.fontSize + 'px' }">
                                    <span :style="{ color: rect.color }" v-if="rect.code != 'input'">{{ rect.name
                                        }}</span>
                                    <div v-else>
                                        <el-input
                                            :style="`--el-input-text-color:${rect.color};font-size:${rect.fontSize}px`"
                                            v-model="rect.name" />
                                    </div>
                                    <el-dropdown trigger="click" style="font-size: 20px;"
                                        @command="changeCommand($event, index)">
                                        <el-icon-edit color="#FFFFFF"></el-icon-edit>

                                        <template #dropdown>
                                            <el-dropdown-menu style="width:100px;">
                                                <el-dropdown-item command="color" disabled><el-color-picker
                                                        v-model="form.design[index].color" /></el-dropdown-item>
                                                <el-dropdown-item command="fontSize" disabled><el-select
                                                        v-model="form.design[index].fontSize" suffix-icon=""
                                                        placeholder="字号" style="width: 100%">
                                                        <el-option :value="12" label="12px" />
                                                        <el-option :value="13" label="13px" />
                                                        <el-option :value="14" label="14px" />
                                                        <el-option :value="15" label="15px" />
                                                        <el-option :value="16" label="16px" />
                                                        <el-option :value="17" label="17px" />
                                                        <el-option :value="18" label="18px" />
                                                        <el-option :value="19" label="19px" />
                                                        <el-option :value="20" label="20px" />
                                                        <el-option :value="22" label="22px" />
                                                        <el-option :value="24" label="24px" />
                                                        <el-option :value="26" label="26px" />
                                                        <el-option :value="28" label="28px" />
                                                        <el-option :value="30" label="30px" />
                                                        <el-option :value="32" label="32px" />
                                                        <el-option :value="34" label="34px" />
                                                    </el-select>
                                                </el-dropdown-item>
                                                <el-dropdown-item divided command="delete">删除</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>

                                </div>
                            </VueDragResize>
                        </div>
                    </div>
                    <div class="card-drawer-bottom">
                        <el-upload action="#" :auto-upload="false" :on-change="chooseFile" :limit="1"
                            :show-file-list="false" accept="image/jpeg">
                            <el-button icon="el-icon-upload" size="small" type="primary">底图</el-button>
                        </el-upload>
                        <el-button size="small" :disabled="!file" @click="addName" icon="el-icon-plus">姓名</el-button>
                        <el-button size="small" :disabled="!file" @click="addNumber" icon="el-icon-plus">工号</el-button>
                        <el-button size="small" :disabled="!file" @click="addText" icon="el-icon-plus">文本</el-button>
                    </div>
                </div>
            </el-col>
            <el-col :span="8">
                <el-form :model="form" :rules="rules" label-position="top" ref="form" @submit.native.prevent="onSave">
                    <el-form-item label="样式名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入样式名称" clearable></el-input>
                    </el-form-item>
                    <el-form-item label="设计参数" prop="design">
                        <div class="template-aside-json">
                            <vue-json-pretty :data="form.design"></vue-json-pretty>
                        </div>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import VueDragResize from 'vue-drag-resize-via'
export default {
    components: {
        VueDragResize
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加新的样式',
                edit: '编辑生日卡样式'
            },
            visitable: false,
            loading: false,
            file: null,
            form: {
                id: 0,
                name: '',
                design: [],
                file: ''
            },
            design: [],
            rules: {
                name: [
                    { required: true, message: '请输入样式名称', trigger: 'blur' }
                ],
                design: [
                    { required: true, type: 'array', message: '请上传设计参数', trigger: 'change' }
                ]
            }
        }
    },
    watch: {
        design: {
            deep: true,
            handler(val) {
                this.form.design = val
            }
        }
    },
    methods: {
        changeCommand(val, index) {
            if (val == 'delete') {
                let design = []
                this.design.forEach((n, i) => {
                    if (i != index) {
                        design.push(n)
                    }
                })
                this.design = design
            }
        },
        changeDesign(newRect, index) {
            this.form.design[index].top = newRect.top
            this.form.design[index].left = newRect.left
            this.form.design[index].width = newRect.width
            this.form.design[index].height = newRect.height
        },
        clickUpload() {
            document.querySelector('.el-upload') && document.querySelector('.el-upload').click()
        },
        addName() {
            this.design.push({
                name: '员工姓名',
                code: 'name',
                width: 100,
                height: 40,
                top: 0,
                left: 0,
                fontSize: 14,
                color: '#F00'
            })
        },
        addNumber() {
            this.design.push({
                name: '员工工号',
                code: 'number',
                width: 100,
                height: 40,
                top: 0,
                left: 0,
                fontSize: 14,
                color: '#F00'
            })
        },
        addText() {
            this.design.push({
                name: '请输入文字',
                code: 'input',
                width: 200,
                height: 40,
                top: 0,
                left: 0,
                fontSize: 14,
                color: '#F00'
            })
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('birthday_card.create') : this.$route('birthday_card.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}样式成功`)
                        this.visitable = false
                        this.$emit('success')
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(type, item) {
            this.mode = type
            if (item) {
                this.form = {
                    id: item.id,
                    name: item.name,
                    design: item.design,
                    file: item.thumbnail
                }
                this.file = item.thumbnail
                this.design = item.design
            } else {
                this.form = {
                    id: 0,
                    name: '',
                    file: '',
                    design: []
                }
                this.file = null
            }
            this.visitable = true
        },
        chooseFile(file) {
            const that = this;
            const reader = new FileReader();
            reader.onloadend = function () {
                that.file = reader.result;
                that.form.file = reader.result;
            };
            reader.readAsDataURL(file.raw);
        },
    }
}
</script>

<style scoped lang="scss">
.template-aside-json {
    height: 300px;
    width: 100%;
    overflow: hidden;
    overflow-y: auto;
}

.card-drawer {
    width: 100%;
    height: 500px;
    @extend .flexColumn;
    flex-direction: column;

    &-content {
        height: 400px;
        width: 500px;
        position: relative;

        :deep(.el-image) {
            height: 100%;
            width: 100%;
        }

        &-error {
            width: 500px;
            height: 400px;
            @extend .flexColumn;
            flex-direction: column;
            background-color: var(--el-fill-color);
            border: var(--el-border-width) var(--el-border-style) var(--el-border-color-hover);
            font-size: 66px;
            position: absolute;
            z-index: 3;

            span {
                font-size: 16px;
                margin-top: 24px;
            }
        }

        &-resize {
            width: 500px;
            height: 400px;
            overflow: hidden;
            position: absolute;
            top: 0;
            left: 0;

            .filler {
                height: 100%;
                width: 100%;
                cursor: pointer;
                position: absolute;
                @extend .flexColumn;
                flex-direction: row;
                background-color: rgba(255, 0, 0, .25);
                text-shadow: 0 1px 3px rgba(0, 0, 0, .25);
                box-shadow: 0 1px 1px 3px rgba(0, 0, 0, .5);
                font-size: 18px;
                color: var(--el-text-color);
                border: var(--el-border-color) 1px dashed;

                :deep(.el-input__wrapper) {
                    background: transparent;
                    padding: 1;
                }

                span {
                    margin-right: 5px;
                }

                &::before,
                &::after {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    content: '';
                    background-repeat: repeat;
                    pointer-events: none;
                    opacity: 0.5;
                }

                &::before {
                    background-image: linear-gradient(to right,
                            rgba(255, 255, 255, .15) 1px,
                            transparent 1px,
                            transparent 10px),
                        linear-gradient(to bottom,
                            rgba(255, 255, 255, .15) 1px,
                            transparent 1px,
                            transparent 10px);
                    background-size: 10px 10px;
                }

                &::after {
                    background-image: linear-gradient(to right,
                            rgba(255, 255, 255, .15) 1px,
                            transparent 1px,
                            transparent 10px),
                        linear-gradient(to bottom,
                            rgba(255, 255, 255, .15) 1px,
                            transparent 1px,
                            transparent 10px);
                    background-size: 10px 10px;
                }

            }
        }
    }

    &-bottom {
        width: 100%;
        height: 100px;
        box-sizing: border-box;
        padding: 10px 0;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;

        :deep(.el-upload) {
            margin-right: 12px;
        }
    }
}
</style>