<template>
    <div class="forget">
        <div class="forget-form">
            <div class="forget-form-header">
                <span>找回密码</span>
                <span>｜</span>
                <span>ForgEt PasswoRd</span>
            </div>
            <el-form v-if="step == 0" ref="forgetForm" :model="form" :rules="rules" label-width="0" size="large"
                 @submit.native.prevent="onNext">
                <el-form-item prop="username">
                    <el-input v-model="form.username" prefix-icon="bbac-icon-user-fill" placeholder="邮箱地址/手机号码">
                    </el-input>
                </el-form-item>
                <el-form-item prop="code">
                    <el-input v-model="form.code" prefix-icon="el-icon-bell" placeholder="请输入验证码">
                        <template #suffix>
                            <img @click="refreshCaptcha" :src="captchaUrl" alt="">
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="12" :span="12" style="text-align: right;">
                        <span class="forget-form-text" @click="$goTo('login')">记得密码？去登录</span>
                    </el-col>
                </el-form-item>
                <el-form-item>
                    <el-button class="forget-btn" style="width: 100%;" round @click="onNext">
                        <span>下一步</span>
                    </el-button>
                </el-form-item>
            </el-form>
            <el-form v-if="step == 1" ref="passwordForm" :model="resetForm" :rules="passwordRules" label-width="0"
                size="large"  @submit.native.prevent="onSubmit">
                <el-form-item prop="username">
                    <el-input v-model="resetForm.username" disabled prefix-icon="bbac-icon-user-fill"
                        placeholder="邮箱地址/手机号码">
                    </el-input>
                </el-form-item>
                <el-form-item prop="code">
                    <el-input v-model="resetForm.code" prefix-icon="el-icon-bell" placeholder="请输入校验码">
                        <template #suffix>
                            <el-button class="button" :disabled="resendVisit" @click="sendCode">
                                <span v-if="timer == 60">重新发送</span>
                                <span v-else>{{ timer }}秒后重新发送</span>
                            </el-button>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input v-model="resetForm.password" type="password" prefix-icon="bbac-icon-lock-fill"
                        placeholder="请输入登录密码">
                    </el-input>
                </el-form-item>
                <el-form-item prop="password_confirmation">
                    <el-input v-model="resetForm.password_confirmation" type="password" prefix-icon="bbac-icon-lock-fill"
                        placeholder="重复输入登录密码"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-col :offset="12" :span="12" style="text-align: right;">
                        <span class="forget-form-text" @click="$goTo('login')">记得密码？去登录</span>
                    </el-col>
                </el-form-item>
                <el-form-item>
                    <el-button class="forget-btn" style="width: 100%;" :loading="resetForm.processing" round @click="onSubmit">
                        <span>立即找回</span>
                    </el-button>
                    <div class="forget-link">
                        <el-button type="primary" link @click="onPrev">
                            <span>上一步</span>
                        </el-button>
                    </div>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script>
import { useForm } from '@inertiajs/vue3'
export default {
    props: {
        errors: Object,
        captcha: Boolean
    },
    data() {
        let validateForget = (rule, value, callback) => {
            let r1 = /^1[3-9][0-9]{9}$/;
            let r2 = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
            if (!r1.test(value) && !r2.test(value)) {
                callback(new Error("请输入正确的邮箱或手机号码"));
            } else {
                callback();
            }
        };
        let validatePassword = (rule, value, callback) => {
            let r1 = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*.])[\da-zA-Z~!@#$%^&*.]{6,}$/;
            if (!r1.test(value)) {
                callback(new Error("密码必须是6位以上、必须含有字母、数字、特殊符号"));
            } else {
                callback();
            }
        };
        let validatePasswordConfirmation = (rule, value, callback) => {
            if (value != this.resetForm.password) {
                callback(new Error("两次输入的密码不相符"));
            } else {
                callback();
            }
        };
        return {
            form: useForm({
                username: '',
                code: ''
            }),
            resetForm: useForm({
                username: '',
                password: '',
                code: '',
                password_confirmation: '',
                reset_token: ''
            }),
            step: 0,
            rules: {
                username: [
                    { required: true, message: '邮箱地址/手机号码不能为空', trigger: 'blur' },
                    { validator: validateForget }
                ],
                code: [
                    { required: true, message: '验证码不能为空', trigger: 'blur' }
                ]
            },
            passwordRules: {
                code: [
                    { required: true, message: '校验码不能为空', trigger: 'blur' },
                ],
                password: [
                    { required: true, message: '登录密码不能为空', trigger: 'blur' },
                    { validator: validatePassword }
                ],
                password_confirmation: [
                    { required: true, message: '重复登录密码不能为空', trigger: 'blur' },
                    { validator: validatePasswordConfirmation }
                ]
            },
            captchaUrl: this.$route('captcha') + '?_t=' + (new Date()).getTime(),
            timer: 60,
            resendVisit: false
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.showMessage()
        })
    },
    methods: {
        refreshCaptcha() {
            this.captchaUrl = this.$route('captcha') + '?_t=' + (new Date()).getTime()
        },
        refreshTimer() {
            if (this.timer == 0) {
                this.resendVisit = true
                this.timer = 60
            } else {
                this.timer--
                setTimeout(() => {
                    this.refreshTimer()
                }, 1000)
            }
        },
        onPrev() {
            this.step = 0
        },
        sendCode() {
            if (this.timer == 60) {
                this.form.post(this.$route('forget.send'), {
                    onError: page => {
                        this.$tool.error(page)
                    },
                    onSuccess: page => {
                        this.form.reset_token = page.props.extra.token
                        this.refreshTimer()
                    }
                })
            }
        },
        async onNext() {
            var validate = await this.$refs.forgetForm.validate().catch(() => { })
            if (!validate) return false;
            this.form.post(this.$route('forget.check'), {
                onError: page => {
                    this.$tool.error(page)
                    this.refreshCaptcha()
                },
                onSuccess: page => {
                    this.step = 1
                    this.resetForm.username = page.props.extra.username
                    this.resetForm.reset_token = page.props.extra.token
                    this.refreshTimer()
                }
            })
        },
        async onSubmit() {
            var validate = await this.$refs.passwordForm.validate().catch(() => { })
            if (!validate) return false;
            this.resetForm.post(this.$route('forget.reset'), {
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
                var captcha = Object.keys(this.$page.props.flash.error)[1]
                this.$message.error(this.$page.props.flash.error[name][0])
                if (this.$page.props.flash.error[captcha][0]) {
                    this.showCaptcha = true
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>
@import '@css/app.scss';

.forget {
    width: 100%;
    flex: 1;
    background: #0c0c0c url(/assets/imgs/login_bg.jpg) top center no-repeat;
    background-size: auto 100%;
    display: flex;
    position: relative;
    overflow: hidden;
    align-items: center;
    justify-content: center;

    &-form {
        margin-top: 80px;
        width: 480px;
        height: 500px;
        background-color: #FFF;
        border-radius: 8px;
        padding: 40px;
        @extend .flexColumn;

        :deep(.el-form) {
            --el-component-size-large: 38px;
            --el-form-inline-content-width: 400px;
            width: 400px;
            flex: 1;
        }

        :deep(.el-input--large) {
            font-size: 18px;
        }

        :deep(.el-form-item) {
            height: 48px;
            margin-bottom: 18px;

            img {
                margin-right: -12px;
                width: 127px;
                height: 34px;
                border-top-right-radius: var(--el-input-border-radius);
                border-bottom-right-radius: var(--el-input-border-radius);
            }
            .button{
                margin-right: -12px;
                width: 127px;
                height: 34px;
                border-top-right-radius: var(--el-input-border-radius);
                border-bottom-right-radius: var(--el-input-border-radius);
            }
        }

        :deep(.el-form-item__error) {
            height: 22px;
            font-size: 14px;
        }

        :deep(.el-icon) {
            font-size: 24px;
        }

        &-text {
            font-size: 14px;
            color: var(--el-color-main);
        }

        :deep(.el-checkbox.el-checkbox--large .el-checkbox__label) {
            font-size: 14px;
        }


        &-header {
            font-family: $font-english;
            height: 42px;
            line-height: 42px;
            color: #2E2E2E;
            font-size: 24px;
            text-align: center;
            margin-bottom: 15px;

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
        height: 52px;
        border-radius: 6px;
        @extend .flexColumn;
        background: $btn-linear;
        color: $color-white;
        font-size: 20px;
        flex-direction: row;
    }
    &-link{
        width: 100%;
        @extend .flexColumn;
    }
}
</style>