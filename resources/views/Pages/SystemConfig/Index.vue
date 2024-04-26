<template>
    <Layout>
      <TopNav></TopNav>
      <div class="page-block">
        <el-form ref="form" :model="form" :rules="formRules" label-width="200px" size="large">
          <el-form-item label="验证码开关" prop="captcha_switch">
            <div style="width:100%">
              <el-switch v-model="form.captcha_switch" active-value="on" inactive-value="off" active-text="开启"
                inactive-text="关闭"></el-switch>
            </div>
            <div class="el-form-item-msg">开启后登录必填验证码</div>
          </el-form-item>
          <el-form-item label="登录失败验证码开关" prop="captcha_of_failed_switch">
            <div style="width:100%">
              <el-switch v-model="form.captcha_of_failed_switch" active-value="on" inactive-value="off" active-text="开启"
                inactive-text="关闭"></el-switch>
            </div>
            <div class="el-form-item-msg">开启后，登录失败后将显示验证码</div>
          </el-form-item>
          <el-form-item label="登录失败次数限制" prop="fail_times_for_lock">
            <el-input type="number" v-model="form.fail_times_for_lock"></el-input>
            <div class="el-form-item-msg">用户登录失败达限制次数时，自动封停账户</div>
          </el-form-item>
          <el-form-item label="基础工时" prop="default_period">
            <el-input type="number" v-model="form.default_period"></el-input>
            <div class="el-form-item-msg">员工基础工时</div>
          </el-form-item>
          <el-form-item>
            <el-button @click="onSubmit" :loading="form.processing" type="primary">保存</el-button>
          </el-form-item>
        </el-form>
      </div>
    </Layout>
  </template>
  <script>
  export default {
    props: {
      item: {
        type: Object,
        default: () => {
          return {
            captcha_switch: true,
            captcha_of_failed_switch: true,
            fail_times_for_lock: 10,
            default_period: 8
          }
        }
      }
    },
    data() {
      return {
        form: {
          captcha_switch: this.item.captcha_switch ? 'on' : 'off',
          captcha_of_failed_switch: this.item.captcha_of_failed_switch ? 'on' : 'off',
          fail_times_for_lock: this.item.fail_times_for_lock,
          default_period: this.item.default_period
        },
        formRules: {
          captcha_switch: [
            { required: true, trigger: 'change', message: '验证码开关不能为空' },
          ],
          captcha_of_failed_switch: [
            { required: true, trigger: 'change', message: '登录失败验证码开关不能为空' },
          ],
          fail_times_for_lock: [
            { required: true, trigger: 'change', message: '登录失败次数限制不能为空' },
          ],
          default_period: [
            { required: true, trigger: 'change', message: '员工基础工时不能为空' },
          ]
        }
      }
    },
    mounted() {
      this.$nextTick(() => { })
    },
    methods: {
      async onSubmit() {
        var validate = await this.$refs.form.validate().catch(() => { })
        if (!validate) return false;
        let res = await this.$axios.post(this.$route('system_config.index'),this.form)
        if(res.code == this.$config.successCode)
        {
          this.$message.success("修改资料成功")
        }else{
          this.$message.error(res.message)
        }
      },
    }
  }
  </script>
  
  <style scoped>
  .el-form-item-msg {
    color: var(--el-link-color)
  }
  </style>