<template>
    <div class="login">
        <div class="login-form">
            <div class="login-form-header">
                <span>用户登录</span>
                <span>｜</span>
                <span>User LOGIn</span>
            </div>
            <el-form ref="loginForm" :model="form" :rules="rules" label-width="0" size="large" @submit.native.prevent="login">
                <el-form-item prop="username">
                    <el-input v-model="form.username" prefix-icon="bbac-icon-user-fill" placeholder="请输入用户名">
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input v-model="form.password" type="password" prefix-icon="bbac-icon-lock-fill"
                        placeholder="请输入密码"></el-input>
                </el-form-item>
                <el-form-item v-if="showCaptcha">
                    <el-input v-model="form.code" prefix-icon="el-icon-bell" placeholder="请输入验证码">
                        <template #suffix>
                            <img @click="refreshCaptcha" :src="captchaUrl" alt="">
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item style="margin-bottom: 10px;">
                    <el-col :span="12">
                        <el-checkbox label="记住密码" v-model="form.remember"></el-checkbox>
                    </el-col>
                    <el-col :span="12" style="text-align: right;">
                        <span class="login-form-text" @click="$goTo('forget')">忘记密码？</span>
                    </el-col>
                </el-form-item>
                <el-form-item>
                    <el-button class="login-btn" style="width: 100%;" :loading="form.processing" round @click="login">
                        <span>立即登录</span>
                    </el-button>
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
        return {
            form: useForm({
                username: '',
                password: '',
                remember: false,
                code: ''
            }),
            showCaptcha: this.captcha,
            rules: {
                username: [
                    { required: true, message: '用户名不能为空', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '登录密码不能为空', trigger: 'blur' }
                ]
            },
            captchaUrl: this.$route('captcha') + '?_t=' + (new Date()).getTime()
        }
    },
    watch: {
        showCaptcha(val){
            if(val)
            {
                this.rules.code = [
                    { required: true, message: '验证码不能为空', trigger: 'blur' }
                ]
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.showMessage()
        })
    },
    methods: {
        refreshCaptcha(){
            this.captchaUrl = this.$route('captcha') + '?_t=' + (new Date()).getTime()
        },
        async login() {
            var validate = await this.$refs.loginForm.validate().catch(() => { })
            if (!validate) return false;
            this.form.post('login', {
                onError: page => {
                    this.$tool.error(page)
                    if (page.captcha) {
                        if(this.showCaptcha)
                        {
                            this.refreshCaptcha()
                        }
                        this.showCaptcha = true
                    }
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

    &-form {
        margin-top: 80px;
        width: 480px;
        height: 500px;
        background-color: #FFF;
        border-radius: 8px;
        padding: 40px;
        @extend .flexColumn;

        :deep(.el-form) {
            --el-component-size-large: 42px;
            --el-form-inline-content-width: 400px;
            width: 400px;
        }

        :deep(.el-input--large) {
            font-size: 20px;
        }

        :deep(.el-form-item) {
            height: 58px;

            img {
                margin-right: -12px;
                width: 127px;
                height: 38px;
                border-top-right-radius: var(--el-input-border-radius);
                border-bottom-right-radius: var(--el-input-border-radius);
            }
        }

        :deep(.el-form-item__error) {
            height: 36px;
            font-size: 16px;
        }

        :deep(.el-icon) {
            font-size: 24px;
        }

        &-text {
            font-size: 18px;
            color: var(--el-color-danger);
        }

        :deep(.el-checkbox.el-checkbox--large .el-checkbox__label) {
            font-size: 18px;
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
        height: 64px;
        border-radius: 6px;
        @extend .flexColumn;
        background: $btn-linear;
        color: $color-white;
        font-size: 24px;
        flex-direction: row;
    }
}
</style>