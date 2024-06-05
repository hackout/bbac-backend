<template>
    <el-dialog title="NOK详情反馈" v-model="visitable" width="400px">
        <el-form :model="form" ref="form" size="small" label-position="top" :rules="rules" v-loading="loading"
            @submit.native.prevent="onSave">
            <el-form-item label="考核结果">
                <el-text size="large" :type="item.is_ok ? 'success' : 'danger'">{{ item.is_ok ? 'OK' : 'NOK'
                    }}</el-text>
            </el-form-item>
            <el-form-item label="缺陷等级" prop="defect_level">
                <el-select style="width:100%" v-model="form.defect_level" placeholder="请选择缺陷等级">
                    <el-option v-for="(item, index) in defect_level" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="缺陷零件/Defective Part" prop="defect_part">
                <el-select style="width:100%" v-model="form.defect_part" placeholder="请选择问题零件">
                    <el-option v-for="(item, index) in problem_parts" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item prop="defect_position">
                <el-select style="width:100%" v-model="form.defect_position" placeholder="请选择问题位置">
                    <el-option v-for="(item, index) in question_position" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item prop="defect_cause">
                <el-select style="width:100%" v-model="form.defect_cause" placeholder="请选择具体位置">
                    <el-option v-for="(item, index) in exactlyList" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="缺陷描述/Defect Description" prop="defect_description">
                <el-select style="width:100%" v-model="form.defect_description" placeholder="请选择缺陷描述" clearable>
                    <el-option v-for="(item, index) in defect_category" :key="index" :value="item.value"
                        :label="item.name"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="缺陷照片/Defect Picture">
                <el-image @click="previewThumbnail(index)" v-for="(attach, index) in item.defect_attaches" :key="index"
                    :src="attach.url"
                    style="width: 100px;height:100px;cursor:pointer;margin-right:6px;margin-bottom:6px;display:inline-block;"></el-image>

            </el-form-item>
            <el-form-item label="备注" prop="note">
                <el-input type="textarea" v-model="form.note" maxlength="250" show-word-limit placeholder="请输入备注"
                    clearable></el-input>
            </el-form-item>
            <el-divider></el-divider>
            <el-form-item label="短期措施" prop="soma">
                <el-input type="textarea" v-model="form.soma" maxlength="250" show-word-limit placeholder="请输入短期措施"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="长期措施" prop="lama">
                <el-input type="textarea" v-model="form.lama" maxlength="250" show-word-limit placeholder="请输入长期措施"
                    clearable></el-input>
            </el-form-item>
            <el-form-item label="维护记录" prop="file">
                <el-input type="file" ref="uploadFile" v-model="form.file" placeholder="请上传维护记录" clearable></el-input>
            </el-form-item>
            <el-form-item label="更改状态" prop="status">
                <el-radio-group v-model="form.status">
                    <el-radio v-for="(item, index) in issue_status" :key="index" :value="item.value"
                        :label="item.value">{{ item.name }}</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-divider></el-divider>
            <el-form-item label="8D NO." prop="eight_disciplines">
                <el-input type="textarea" v-model="form.eight_disciplines" maxlength="250" show-word-limit
                    placeholder="请输入8D NO." clearable></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
        <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex"
            :url-list="viewerList" />
    </el-dialog>
</template>

<script>
export default {
    props: {
        issue_status: {
            type: Array,
            default: []
        },
        defect_level: {
            type: Array,
            default: []
        },
        defect_category: {
            type: Array,
            default: []
        },
        problem_parts: {
            type: Array,
            default: []
        },
        question_position: {
            type: Array,
            default: []
        },
        exactly_1: {
            type: Array,
            default: []
        },
        exactly_2: {
            type: Array,
            default: []
        },
        exactly_3: {
            type: Array,
            default: []
        },
        exactly_4: {
            type: Array,
            default: []
        },
        exactly_5: {
            type: Array,
            default: []
        },
        exactly_6: {
            type: Array,
            default: []
        },
        exactly_7: {
            type: Array,
            default: []
        },
        exactly_8: {
            type: Array,
            default: []
        },
        exactly_9: {
            type: Array,
            default: []
        },
        exactly_10: {
            type: Array,
            default: []
        },
        exactly_11: {
            type: Array,
            default: []
        },
        exactly_12: {
            type: Array,
            default: []
        },
        exactly_13: {
            type: Array,
            default: []
        },
        exactly_14: {
            type: Array,
            default: []
        },
        exactly_15: {
            type: Array,
            default: []
        },
        exactly_16: {
            type: Array,
            default: []
        },
        exactly_17: {
            type: Array,
            default: []
        },
        exactly_18: {
            type: Array,
            default: []
        },
        exactly_19: {
            type: Array,
            default: []
        },
        exactly_20: {
            type: Array,
            default: []
        },
        exactly_21: {
            type: Array,
            default: []
        },
        exactly_22: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            visitable: false,
            loading: false,
            item: {
                id: '',
                defect_level: '',
                defect_part: '',
                defect_position: '',
                defect_cause: '',
                defect_description: '',
                note: '',
                soma: '',
                lama: '',
                status: '',
                eight_disciplines: '',
                defect_attaches: [],
                is_ok: true
            },
            form: {
                defect_level: '',
                defect_part: '',
                defect_position: '',
                defect_cause: '',
                defect_description: '',
                note: '',
                soma: '',
                lama: '',
                file: '',
                status: '',
                eight_disciplines: '',
            },
            rules: {
                defect_level: [
                    { required: true, message: '请选择缺陷等级', trigger: 'change' }
                ]
            },
            showViewer: false,
            viewerIndex: 0,
            viewerList: [],
            exactlyList: []
        }
    },
    watch: {
        "form.defect_part": {
            handler(val) {
                this.exactlyList = val > 0 ? this.$page.props['exactly_' + val] : []
            }
        }
    },
    methods: {
        previewThumbnail(i) {
            this.viewerList = this.item.defect_attaches.map(n => n.url)
            this.viewerIndex = i
            this.showViewer = true
        },
        open(item) {
            this.item = item
            this.loading = false;
            this.form = {
                defect_level: item.defect_level,
                defect_part: item.defect_part,
                defect_position: item.defect_position,
                defect_cause: item.defect_cause,
                defect_description: item.defect_description,
                note: item.note,
                soma: item.soma,
                lama: item.lama,
                status: item.status,
                eight_disciplines: item.eight_disciplines,
                file: null
            }
            this.exactlyList = this.form.defect_position > 0 ? this.$page.props['exactly_' + this.form.defect_position] : []
            this.visitable = true
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    let form = new FormData()
                    Object.keys(this.form).forEach(n => {
                        if (n != 'file') {
                            form.append(n, this.form[n])
                        } else {
                            if (this.form.file) {
                                form.append('file', this.$refs.uploadFile.input.files[0])
                            }
                        }
                    })
                    let res = await this.$axios.post(this.$route('stuff.update', { id: this.item.id }), form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success('维护记录成功')
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        }
    }
}
</script>

<style scoped lang="scss"></style>