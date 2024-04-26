<template>
    <el-dialog :title="titles[mode]" v-model="visitable" width="450px" @closed="$emit('closed')">
        <el-form :model="form" ref="form" :rules="rules" label-width="100" @submit.native.prevent="onSave">
            <el-form-item label="角色名称" prop="name">
                <el-input v-model="form.name" placeholder="请输入角色名称" clearable></el-input>
            </el-form-item>
            <el-form-item label="角色状态" prop="is_valid">
                <el-switch v-model="form.is_valid"></el-switch>
            </el-form-item>
            <el-form-item label="角色权限" prop="permissions">
                <div class="permission-box">
                    <el-radio-group v-model="checkAll" @change="changeCheck">
                        <el-radio :label="1">全选</el-radio>
                        <el-radio :label="0">全不选</el-radio>
                    </el-radio-group>
                </div>
                <el-tree-v2 style="width:100%;" :data="permissions" check-strictly :props="treeProp" show-checkbox
                    :height="200" ref="treeRef" :default-checked-keys="form.permissions"
                    :default-expanded-keys="expandedKeys" @check-change="changePermission">
                </el-tree-v2>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="primary" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
export default {
    props: {
        permissions: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        return {
            mode: 'add',
            titles: {
                add: '添加新的角色',
                edit: '编辑角色信息'
            },
            visitable: false,
            loading: false,
            options: [],
            treeProp: {
                label: 'name',
                value: 'code'
            },
            checkAll: 0,
            expandedKeys: [],
            form: {
                id: 0,
                is_valid: true,
                name: '',
                permissions: []
            },
            rules: {
                name: [
                    { required: true, message: '请输入角色名称', trigger: 'blur' }
                ],
                is_valid: [
                    { required: true, message: '请选择角色状态', trigger: 'change' }
                ]
            }
        }
    },
    methods: {
        changeCheck(v) {
            if (v == 1) {
                this.form.permissions = []
                this.permissions.forEach(n => {
                    if (n.code) {
                        this.form.permissions.push(n.code)

                    }
                    if (n.children && n.children.length > 0) {
                        n.children.forEach(x => {
                            if (x.code) {
                                this.form.permissions.push(x.code)
                            }
                        })
                    }
                })
            } else {
                this.form.permissions = []
            }
            this.$refs.treeRef.setCheckedKeys(this.form.permissions)
        },
        changePermission() {
            this.form.permissions = this.$refs.treeRef.getCheckedNodes() ? this.$refs.treeRef.getCheckedNodes().map(n => n.code) : []
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    let method = this.mode == 'add' ? 'post' : 'put'
                    let url = this.mode == 'add' ? this.$route('role.create') : this.$route('role.update', { id: this.form.id })
                    this.loading = true
                    let res = await this.$axios[method](url, this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`${this.mode == 'add' ? '添加' : '修改'}角色信息成功`)
                        this.$emit('success')
                        this.visitable = false
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
                    is_valid: item.is_valid,
                    name: item.name,
                    permissions: item.permissions
                }
            } else {
                this.form = {
                    id: 0,
                    is_valid: true,
                    name: '',
                    permissions: []
                }
            }
            this.expandedKeys = []
            this.permissions.forEach(n => {
                this.expandedKeys.push(n.code)
            })
            this.visitable = true
        }
    }
}
</script>

<style scoped lang="scss">
.permission-box {
    width: 340px;
    border-bottom: #efefef 1px solid;
    padding-bottom: 5px;
    margin-bottom: 5px;
    margin-left: 15px !important;
}

:deep(.el-radio) {
    margin-right: 10px;
}
</style>