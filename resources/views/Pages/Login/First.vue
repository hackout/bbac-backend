<template>
    <div class="login">
        <div class="login-form" v-if="step == 0">
            <div class="login-form-header">
                <span>完善信息</span>
                <span>｜</span>
                <span>InformatIon</span>
            </div>
            <el-form ref="firstForm" :model="form" :rules="rules" label-position="top"  @submit.native.prevent="onNext">
                <el-scrollbar height="400px">
                    <div class="login-box">
                        <el-row :gutter="24">
                            <el-col :span="24">
                                <el-divider content-position="left">基础信息</el-divider>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="account" label="登录账号">
                                    <el-input v-model="user.username" disabled placeholder="请输入登录账号">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="number" label="员工工号">
                                    <el-input v-model="form.number" placeholder="请输入员工工号">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="mobile" label="手机号码">
                                    <el-input v-model="form.mobile" placeholder="请输入手机号码">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="email" label="邮箱地址">
                                    <el-input v-model="form.email" placeholder="请输入邮箱地址">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-divider content-position="left">工作信息</el-divider>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="name" label="员工姓名">
                                    <el-input v-model="form.name" placeholder="请输入员工姓名">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="pinyin" label="姓名拼音">
                                    <el-input v-model="form.pinyin" placeholder="请输入姓名拼音">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="gender" label="性别称谓">
                                    <el-select v-model="form.gender" filterable placeholder="请选择性别称谓" style="width: 100%">
                                        <el-option v-for="(nation, index) in options.gender" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="birth" label="出生日期">
                                    <el-date-picker style="width: 100%;" v-model="form.birth" type="date"
                                        placeholder="请选择出生日期" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="nation" label="民族">
                                    <el-select v-model="form.nation" filterable placeholder="请选择民族" style="width: 100%">
                                        <el-option v-for="(nation, index) in options.nation" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="birthplace" label="户籍籍贯">
                                    <el-input v-model="form.birthplace" placeholder="请输入户籍籍贯">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="address" label="家庭地址">
                                    <el-input v-model="form.address" placeholder="请输入家庭地址">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="id_card" label="证件号码">
                                    <el-input v-model="form.id_card" placeholder="请输入证件号码">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="attend_date" label="工作时间">
                                    <el-date-picker style="width: 100%;" v-model="form.attend_date" type="date"
                                        placeholder="请选择参加工作时间" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="entry_date" label="入职时间">
                                    <el-date-picker style="width: 100%;" v-model="form.entry_date" type="date"
                                        placeholder="请选择入职时间" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-divider content-position="left">紧急联系人</el-divider>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="emergency_contact" label="紧急联系人">
                                    <el-input v-model="form.emergency_contact" placeholder="请输入紧急联系人">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="emergency_telephone" label="紧急联系电话">
                                    <el-input v-model="form.emergency_telephone" placeholder="请输入紧急联系电话">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-divider content-position="left">学历信息</el-divider>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="educational" label="学历">
                                    <el-input v-model="form.educational" placeholder="请输入学历">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="science" label="学位">
                                    <el-input v-model="form.science" placeholder="请输入学位">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-divider content-position="left">技能信息</el-divider>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="skill_level" label="综合技能等级">
                                    <el-select v-model="form.skill_level" filterable placeholder="请选择综合技能等级"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.skill_level" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="career_level" label="职业等级">
                                    <el-select v-model="form.career_level" filterable placeholder="请选择职业等级"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.career_level" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-form-item>
                                    <el-button class="login-btn" style="width: 100%;" round @click="onNext">
                                        <span>下一步</span>
                                    </el-button>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </el-scrollbar>
            </el-form>
        </div>
        <div class="login-form" v-if="step == 1">
            <div class="login-form-header">
                <span>设置密码</span>
                <span>｜</span>
                <span>PasswoRd</span>
            </div>
            <el-form ref="passwordForm" :model="form" :rules="passwordRules" label-position="top"  @submit.native.prevent="onSubmit">
                <el-form-item prop="password" label="登录密码">
                    <el-input v-model="form.password" type="password" placeholder="请输入登录密码">
                    </el-input>
                </el-form-item>
                <el-form-item prop="password_confirmation" label="重复登录密码">
                    <el-input v-model="form.password_confirmation" type="password" placeholder="请输入重复登录密码">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-button class="login-btn" style="width: 100%;" :loading="form.processing" round @click="onSubmit">
                        <span>提交保存</span>
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script>
import { useForm } from '@inertiajs/vue3'
import { pinyin } from 'pinyin-pro';
export default {
    props: {
        errors: Object,
        options: Object,
        user: Object
    },
    data() {
        let validatePassword = (rule, value, callback) => {
            let r1 = null;
            if (this.user.is_super) {
                r1 = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*.])[\da-zA-Z~!@#$%^&*.]{15,}$/;
            } else {
                r1 = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*.])[\da-zA-Z~!@#$%^&*.]{6,}$/;
            }
            if (!r1.test(value)) {
                callback(new Error("密码必须是"+(this.user.is_super ? '15' : '6')+"位以上、必须含有字母、数字、特殊符号"));
            } else {
                callback();
            }
        };
        let validatePasswordConfirmation = (rule, value, callback) => {
            if (value != this.form.password) {
                callback(new Error("两次输入的密码不相符"));
            } else {
                callback();
            }
        };
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
            step: 0,
            form: useForm({
                number: '',
                mobile: '',
                email: '',
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
                entry_date: '',
                password: '',
                password_confirmation: ''
            }),
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
            },
            passwordRules: {
                password: [
                    { required: true, message: '登录密码不能为空', trigger: 'blur' },
                    { validator: validatePassword }
                ],
                password_confirmation: [
                    { required: true, message: '重复登录密码不能为空', trigger: 'blur' },
                    { validator: validatePasswordConfirmation }
                ]
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.showMessage()
        })
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
        async onNext() {
            var validate = await this.$refs.firstForm.validate().catch(() => { })
            if (!validate) return false;
            this.step = 1;
        },
        async onSubmit() {
            var validate = await this.$refs.passwordForm.validate().catch(() => { })
            if (!validate) return false;
            this.form.post(this.$route('login.first'), {
                onError: page => {
                    this.$tool.error(page)
                }
            })
        },
        showMessage() {
            if (this.$page.props.flash.success) {
                this.$message.success(this.$page.props.flash.success)
            } else if (this.$page.props.flash.error) {
                var name = Object.keys(this.$page.props.flash.error)[0]
                this.$message.error(this.$page.props.flash.error[name][0])
            }
        }
    }
}
</script>

<style lang="scss" scoped>
@import '@css/app.scss';

.login {
    width: 100%;
    flex: 1;
    background: #0c0c0c url(/assets/imgs/login_bg.jpg) top center no-repeat;
    background-size: auto 100%;
    display: flex;
    position: relative;
    overflow: hidden;
    align-items: center;
    justify-content: center;

    &-box {
        width: 100%;
        overflow: hidden;
        box-sizing: border-box;
        padding-right: 25px;
    }

    &-form {
        margin-top: 80px;
        width: 680px;
        height: 500px;
        background-color: #FFF;
        border-radius: 8px;
        padding: 40px;
        padding-right: 15px;
        @extend .flexColumn;

        :deep(.el-form) {
            --el-component-size-large: 32px;
            --el-form-inline-content-width: 285px;
            width: 600px;
        }

        :deep(.el-form-item) {
            height: 64px;
        }

        :deep(.el-form-item__error) {
            height: 22px;
            font-size: 14px;
        }

        &-text {
            font-size: 16px;
            color: var(--el-color-danger);
        }

        :deep(.el-checkbox.el-checkbox--large .el-checkbox__label) {
            font-size: 16px;
        }


        &-header {
            font-family: $font-english;
            height: 48px;
            line-height: 48px;
            color: #2E2E2E;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;

            span {
                font-weight: 200;

                &:first-child {
                    font-weight: 900;
                }
            }
        }
    }

    &-btn {
        width: 100%;
        height: 48px;
        border-radius: 6px;
        @extend .flexColumn;
        background: $btn-linear;
        color: $color-white;
        font-size: 22px;
        flex-direction: row;
    }
}
</style>