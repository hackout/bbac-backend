<template>
    <el-drawer title="员工资料详情" v-model="visitable" :size="800" @closed="$emit('closed')">
        <el-form ref="form" :model="form" :rules="rules" label-position="top" @submit.native.prevent="onSave">
            <el-scrollbar height="calc(100vh - 160px)">
                <div class="form-box">
                    <el-row :gutter="24">
                        <el-col :span="24">
                            <el-divider content-position="left">基础信息</el-divider>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="account" label="登录账号">
                                <el-input :disabled="readable" v-model="form.username" placeholder="请输入登录账号">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="number" label="员工工号">
                                <el-input :disabled="readable" v-model="form.number" placeholder="请输入员工工号">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="mobile" label="手机号码">
                                <el-input :disabled="readable" v-model="form.mobile" placeholder="请输入手机号码">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="email" label="邮箱地址">
                                <el-input :disabled="readable" v-model="form.email" placeholder="请输入邮箱地址">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-divider content-position="left">工作信息</el-divider>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="name" label="员工姓名">
                                <el-input :disabled="readable" v-model="form.name" placeholder="请输入员工姓名">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="department_id" label="所在部门">
                                <el-cascader :disabled="readable" :options="options" placeholder="请选择所在部门"
                                    v-model="form.department_id" style="width:100%;" :props="cascaderProp"
                                    @change="changeParentId" clearable />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="pinyin" label="姓名拼音">
                                <el-input :disabled="readable" v-model="form.pinyin" placeholder="请输入姓名拼音">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="birthplace" label="户籍籍贯">
                                <el-input :disabled="readable" v-model="form.birthplace" placeholder="请输入户籍籍贯">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item prop="gender" label="性别称谓">
                                <el-select :disabled="readable" v-model="form.gender" filterable placeholder="请选择性别称谓"
                                    style="width: 100%">
                                    <el-option v-for="(nation, index) in gender" :key="index" :label="nation.name"
                                        :value="nation.value" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item prop="birth" label="出生日期">
                                <el-date-picker :disabled="readable" style="width: 100%;" v-model="form.birth"
                                    type="date" placeholder="请选择出生日期" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item prop="nation" label="民族">
                                <el-select :disabled="readable" v-model="form.nation" filterable placeholder="请选择民族"
                                    style="width: 100%">
                                    <el-option v-for="(nation, index) in nation" :key="index" :label="nation.name"
                                        :value="nation.value" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="address" label="家庭地址">
                                <el-input :disabled="readable" v-model="form.address" placeholder="请输入家庭地址">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="id_card" label="证件号码">
                                <el-input :disabled="readable" v-model="form.id_card" placeholder="请输入证件号码">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="attend_date" label="工作时间">
                                <el-date-picker :disabled="readable" style="width: 100%;" v-model="form.attend_date"
                                    type="date" placeholder="请选择参加工作时间" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="entry_date" label="入职时间">
                                <el-date-picker :disabled="readable" style="width: 100%;" v-model="form.entry_date"
                                    type="date" placeholder="请选择入职时间" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-form-item prop="roles" label="角色信息">
                                <el-select :disabled="readable" v-model="form.roles" multiple placeholder="请选择角色信息"
                                    style="width: 100%" clearable>
                                    <el-option v-for="item in roles" :key="item.value" :label="item.name"
                                        :value="item.value" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-divider content-position="left">紧急联系人</el-divider>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="emergency_contact" label="紧急联系人">
                                <el-input :disabled="readable" v-model="form.emergency_contact" placeholder="请输入紧急联系人">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="emergency_telephone" label="紧急联系电话">
                                <el-input :disabled="readable" v-model="form.emergency_telephone"
                                    placeholder="请输入紧急联系电话">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-divider content-position="left">学历信息</el-divider>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="educational" label="学历">
                                <el-input :disabled="readable" v-model="form.educational" placeholder="请输入学历">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="science" label="学位">
                                <el-input :disabled="readable" v-model="form.science" placeholder="请输入学位">
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="24">
                            <el-divider content-position="left">技能信息</el-divider>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="skill_level" label="综合技能等级">
                                <el-select :disabled="readable" v-model="form.skill_level" filterable
                                    placeholder="请选择综合技能等级" style="width: 100%">
                                    <el-option v-for="(nation, index) in skill_level" :key="index" :label="nation.name"
                                        :value="nation.value" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item prop="career_level" label="职业等级">
                                <el-select :disabled="readable" v-model="form.career_level" filterable
                                    placeholder="请选择职业等级" style="width: 100%">
                                    <el-option v-for="(nation, index) in career_level" :key="index" :label="nation.name"
                                        :value="nation.value" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>
            </el-scrollbar>
        </el-form>
        <template #footer>
            <div class="drawer-footer">
                <el-button @click="visitable = false">取消</el-button>
                <el-button type="warning" v-if="readable" @click="readable = false">编辑资料</el-button>
                <el-button type="primary" v-if="!readable" v-loading="loading" @click="onSave">提交保存</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>
import { pinyin } from 'pinyin-pro';
export default {
    props: {
        departments: {
            type: Array,
            default: []
        },
        roles: {
            type: Array,
            default: []
        },
        gender: {
            type: Array,
            default: []
        },
        nation: {
            type: Array,
            default: []
        },
        skill_level: {
            type: Array,
            default: []
        },
        career_level: {
            type: Array,
            default: []
        }
    },
    emits: ['success', 'closed'],
    data() {
        let validateMobile = (rule, value, callback) => {
            let r1 = /^1[3-9][0-9]{9}$/;
            if (!r1.test(value)) {
                callback(new Error("仅支持国内手机号码"));
            } else {
                callback();
            }
        };
        let validateEmail = (rule, value, callback) => {
            let r1 = /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/;
            if (!r1.test(value)) {
                callback(new Error("请输入正确的电子邮箱地址"));
            } else {
                callback();
            }
        };
        let validateIdCard = (rule, value, callback) => {
            let r1 = /(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/;
            if (!r1.test(value)) {
                callback(new Error("请输入正确的证件号码"));
            } else {
                callback();
            }
        };
        return {
            visitable: false,
            loading: false,
            options: [],
            cascaderProp: {
                checkStrictly: true,
                label: 'name',
                value: 'id'
            },
            id: '',
            readable: true,
            form: {
                department_id: '',
                roles: [],
                username: '',
                email: '',
                mobile: '',
                number: '',
                name: '',
                pinyin: '',
                gender: '',
                birth: '',
                nation: '',
                birthplace: '',
                address: '',
                id_card: '',
                educational: '',
                science: '',
                emergency_contact: '',
                emergency_telephone: '',
                skill_level: '',
                career_level: '',
                attend_date: '',
                entry_date: ''
            },
            rules: {
                number: [
                    { required: true, message: '员工工号不能为空', trigger: 'blur' }
                ],
                mobile: [
                    { required: true, message: '手机号码不能为空', trigger: 'blur' },
                    { validator: validateMobile }
                ],
                email: [
                    { required: true, message: '邮箱地址不能为空', trigger: 'blur' },
                    { validator: validateEmail }
                ],
                name: [
                    { required: true, message: '员工姓名不能为空', trigger: 'blur' }
                ],
                pinyin: [
                    { required: true, message: '姓名拼音不能为空', trigger: 'blur' }
                ],
                gender: [
                    { required: true, message: '性别不能为空', trigger: 'change' }
                ],
                birth: [
                    { required: true, type: 'date', message: '出生日期不能为空', trigger: 'change' }
                ],
                nation: [
                    { required: true, message: '民族不能为空', trigger: 'change' }
                ],
                birthplace: [
                    { required: true, message: '籍贯不能为空', trigger: 'blur' }
                ],
                address: [
                    { required: true, message: '家庭地址不能为空', trigger: 'blur' }
                ],
                id_card: [
                    { required: true, message: '证件号码不能为空', trigger: 'blur' },
                    { validator: validateIdCard }
                ],
                educational: [
                    { required: true, message: '学历不能为空', trigger: 'blur' }
                ],
                science: [
                    { required: true, message: '学位不能为空', trigger: 'blur' }
                ],
                emergency_contact: [
                    { required: true, message: '紧急联系人不能为空', trigger: 'blur' }
                ],
                emergency_telephone: [
                    { required: true, message: '紧急联系电话不能为空', trigger: 'blur' }
                ],
                skill_level: [
                    { required: true, message: '综合技能等级不能为空', trigger: 'change' }
                ],
                career_level: [
                    { required: true, message: '职业等级不能为空', trigger: 'change' }
                ],
                attend_date: [
                    { required: true, type: 'date', message: '工作时间不能为空', trigger: 'change' }
                ],
                entry_date: [
                    { required: true, type: 'date', message: '入职时间不能为空', trigger: 'change' }
                ],
                department_id: [
                    { required: true, message: '部门信息不能为空', trigger: 'change' }
                ],
                roles: [
                    { required: true, type: 'array', message: '角色信息不能为空', trigger: 'change' }
                ]
            },
        }
    },
    watch: {
        'form.name': {
            handler(val) {
                let pinyinText = pinyin(val, { toneType: 'none', mode: 'surname', v: true, nonZh: 'removed' })
                var str = pinyinText.toLowerCase();
                var strArr = str.split(' ');
                var result = '';
                for (var i in strArr) {
                    result += strArr[i].substring(0, 1).toUpperCase() + strArr[i].substring(1) + ' ';
                }
                this.form.pinyin = result;
            }
        }
    },
    methods: {
        changeParentId(value) {
            this.form.department_id = value[value.length - 1]
        },
        onSave() {
            this.$refs.form.validate(async (valid) => {
                if (valid) {
                    this.loading = true
                    let res = await this.$axios.post(this.$route('user.update_detail', { id: this.id }), this.form)
                    this.loading = false
                    if (res.code == this.$config.successCode) {
                        this.$message.success(`修改用户信息成功`)
                        this.$emit('success')
                        this.visitable = false
                    } else {
                        this.$message.error(res.message)
                    }
                }
            })
        },
        open(item) {
            this.id = item.id
            this.options = [
                {
                    name: '无',
                    id: '',
                    disabled: false
                }
            ]
            this.departments.forEach(n => {
                this.options.push({
                    name: n.name,
                    id: n.id,
                    disabled: n.id == this.form.id,
                    children: this.makeChildren(n, n.id == this.form.id)
                })
            })
            this.visitable = true
            this.getDetail()
        },
        async getDetail() {
            this.loading = true
            let res = await this.$axios.get(this.$route('user.detail', { id: this.id }))
            this.loading = false
            if (res.code == this.$config.successCode) {
                this.form = {
                    department_id: res.data.item.department_id ?? '',
                    roles: res.data.item.roleList,
                    username: res.data.item.username,
                    email: res.data.item.email,
                    mobile: res.data.item.mobile,
                    number: res.data.item.number,
                    name: !res.data.profile ? '' : res.data.profile.name,
                    pinyin: !res.data.profile ? '' : res.data.profile.pinyin,
                    gender: !res.data.profile ? '' : res.data.profile.gender,
                    birth: !res.data.profile ? '' : res.data.profile.birth,
                    nation: !res.data.profile ? '' : res.data.profile.nation,
                    birthplace: !res.data.profile ? '' : res.data.profile.birthplace,
                    address: !res.data.profile ? '' : res.data.profile.address,
                    id_card: !res.data.profile ? '' : res.data.profile.id_card,
                    educational: !res.data.profile ? '' : res.data.profile.educational,
                    science: !res.data.profile ? '' : res.data.profile.science,
                    emergency_contact: !res.data.profile ? '' : res.data.profile.emergency_contact,
                    emergency_telephone: !res.data.profile ? '' : res.data.profile.emergency_telephone,
                    skill_level: !res.data.profile ? '' : res.data.profile.skill_level,
                    career_level: !res.data.profile ? '' : res.data.profile.career_level,
                    attend_date: !res.data.profile ? '' : res.data.profile.attend_date,
                    entry_date: !res.data.profile ? '' : res.data.profile.entry_date,
                }
            } else {
                this.$message.error(res.message)
                this.visitable = false
                this.$emit('closed')
            }
        },
        makeChildren(options, disabled = false) {
            let result = []
            if (options.children) {
                options.children.forEach(n => {
                    result.push({
                        name: n.name,
                        id: n.id,
                        disabled: disabled || n.id == this.form.id,
                        children: this.makeChildren(n, disabled || n.id == this.form.id)
                    })
                })
            }
            return result.length > 0 ? result : null
        }
    }
}
</script>

<style scoped lang="scss">
.form-box {
    height: calc(100vh - 160px);
    width: 650px;
    padding: 0 20px;
    box-sizing: border-box;
}
</style>
