<template>
    <Layout>
        <div class="page-block profile">
            <el-aside style="width: 240px;">

                <el-menu class="profile-menu" :default-active="page">
                    <div class="profile-avatar">
                        <el-avatar :size="64" :src="item.avatar" style="font-size: 22px;margin-bottom: 5px;">{{ avatar
                            }}
                        </el-avatar>
                        <h2>{{ item.name }}</h2>
                        <p style="margin-top:10px;">
                            <el-tag effect="dark" round size="large" disable-transitions
                                v-for="(role, index) in item.roles" :key="index">{{ role }}</el-tag>
                        </p>
                    </div>
                    <el-menu-item-group v-for="group in menu" :key="group.groupName" :title="group.groupName">
                        <el-menu-item v-for="item in group.list" :key="item.component" :index="item.component"
                            @click="openPage">
                            <el-icon v-if="item.icon">
                                <component :is="item.icon" />
                            </el-icon>
                            <template #title>
                                <span>{{ item.title }}</span>
                            </template>
                        </el-menu-item>
                    </el-menu-item-group>
                </el-menu>
            </el-aside>
            <el-main style="padding-top: 0;">
                <el-card shadow="never" v-if="page == 'profile'">
                    <el-form ref="profileForm" size="large" :rules="profileRules" :model="profileForm"
                        label-width="120px" @submit.native.prevent="profileSubmit">
                        <el-row :gutter="24">
                            <el-col :span="24">
                                <span class="profile-subtitle">工作信息</span>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="name" label="员工姓名">
                                    <el-input v-model="profileForm.name" placeholder="请输入员工姓名">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="pinyin" label="姓名拼音">
                                    <el-input v-model="profileForm.pinyin" placeholder="请输入姓名拼音">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="gender" label="性别称谓">
                                    <el-select v-model="profileForm.gender" filterable placeholder="请选择性别称谓"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.gender" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="birth" label="出生日期">
                                    <el-date-picker style="width: 100%;" v-model="profileForm.birth" type="date"
                                        placeholder="请选择出生日期" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="nation" label="民族">
                                    <el-select v-model="profileForm.nation" filterable placeholder="请选择民族"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.nation" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="birthplace" label="户籍籍贯">
                                    <el-input v-model="profileForm.birthplace" placeholder="请输入户籍籍贯">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="address" label="家庭地址">
                                    <el-input v-model="profileForm.address" placeholder="请输入家庭地址">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="id_card" label="证件号码">
                                    <el-input v-model="profileForm.id_card" placeholder="请输入证件号码">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="attend_date" label="工作时间">
                                    <el-date-picker style="width: 100%;" v-model="profileForm.attend_date" type="date"
                                        placeholder="请选择参加工作时间" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="entry_date" label="入职时间">
                                    <el-date-picker style="width: 100%;" v-model="profileForm.entry_date" type="date"
                                        placeholder="请选择入职时间" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <span class="profile-subtitle">紧急联系人</span>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="emergency_contact" label="紧急联系人">
                                    <el-input v-model="profileForm.emergency_contact" placeholder="请输入紧急联系人">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="emergency_telephone" label="紧急联系电话">
                                    <el-input v-model="profileForm.emergency_telephone" placeholder="请输入紧急联系电话">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <span class="profile-subtitle">学历信息</span>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="educational" label="学历">
                                    <el-input v-model="profileForm.educational" placeholder="请输入学历">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="science" label="学位">
                                    <el-input v-model="profileForm.science" placeholder="请输入学位">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <span class="profile-subtitle">技能信息</span>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="skill_level" label="综合技能等级">
                                    <el-select v-model="profileForm.skill_level" filterable placeholder="请选择综合技能等级"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.skill_level" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item prop="career_level" label="职业等级">
                                    <el-select v-model="profileForm.career_level" filterable placeholder="请选择职业等级"
                                        style="width: 100%">
                                        <el-option v-for="(nation, index) in options.career_level" :key="index"
                                            :label="nation.name" :value="nation.value" />
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-form-item>
                                    <el-button style="width:100%;" size="large" :loading="profileForm.processing" round
                                        @click="profileSubmit" type="primary">提交保存</el-button>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </el-card>
                <el-card shadow="never" v-if="page == 'account'" header="账号信息">
                    <div class="profile-list">
                        <div class="profile-list-item">
                            <div class="profile-list-item-label">
                                <span>登录账号</span>
                            </div>
                            <div class="profile-list-item-desc">
                                <span>{{ item.username }}</span>
                                <el-tooltip content="登录账号无法修改" placement="bottom-end">
                                    <el-icon>
                                        <el-icon-warning />
                                    </el-icon>
                                </el-tooltip>
                            </div>
                        </div>
                        <div class="profile-list-item">
                            <div class="profile-list-item-label">
                                <span>手机号码</span>
                            </div>
                            <div class="profile-list-item-desc">
                                <span>{{ item.mobile }}</span>
                                <el-icon @click="checkPassword('onEditMobile')">
                                    <el-icon-edit />
                                </el-icon>
                            </div>
                        </div>
                        <div class="profile-list-item">
                            <div class="profile-list-item-label">
                                <span>邮箱地址</span>
                            </div>
                            <div class="profile-list-item-desc">
                                <span>{{ item.email }}</span>
                                <el-icon @click="checkPassword('onEditEmail')">
                                    <el-icon-edit />
                                </el-icon>
                            </div>
                        </div>
                        <div class="profile-list-item">
                            <div class="profile-list-item-label">
                                <span>员工号</span>
                            </div>
                            <div class="profile-list-item-desc">
                                <span>{{ item.number }}</span>
                                <el-icon @click="checkPassword('onEditNumber')">
                                    <el-icon-edit />
                                </el-icon>
                            </div>
                        </div>
                    </div>
                </el-card>
                <el-card v-if="page == 'password'" shadow="never" header="密码安全">
                    <el-alert title="修改密码后需要重新登录" type="info" show-icon style="margin-bottom: 15px;" />
                    <el-form ref="passwordForm" :model="passwordForm" size="large" :rules="passwordRules"
                        label-width="120px" style="margin-top:20px;" @submit.native.prevent="passwordSubmit">
                        <el-form-item label="当前密码" prop="current_password">
                            <el-input v-model="passwordForm.current_password" type="password" show-password
                                placeholder="请输入当前密码"></el-input>
                        </el-form-item>
                        <el-form-item label="登录密码" prop="password">
                            <el-input v-model="passwordForm.password" type="password" show-password
                                placeholder="请输入新的登录密码"></el-input>
                        </el-form-item>
                        <el-form-item label="重复密码" prop="password_confirmation">
                            <el-input v-model="passwordForm.password_confirmation" type="password" show-password
                                placeholder="请重复输入登录密码"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="passwordSubmit" :loading="passwordForm.processing"
                                type="primary">提交修改</el-button>
                        </el-form-item>
                    </el-form>
                </el-card>
                <el-card v-if="page == 'log'" shadow="never" header="操作记录">
                    <DataTable ref="table" height="570px" :apiName="$route('profile.log')" stripe highlightCurrentRow
                        remoteSort remoteFilter>
                        <el-table-column align="center" label="端口" prop="os" width="150">
                            <template #default="scope">
                                <el-tag effect="dark" size="small">{{ scope.row.os == 'backend' ? '后台' : 'PAD'
                                    }}</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column align="center" show-overflow-tooltip label="操作说明" prop="description">
                            <template #default="scope">
                                <el-tooltip effect="dark" :content="scope.row.route" placement="top-end">
                                    <span>{{ scope.row.name }}</span>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                        <el-table-column align="center" show-overflow-tooltip label="访问结果" prop="status" width="150">
                            <template #default="scope">
                                <el-tag type="success" v-if="scope.row.status" effect="dark" size="small">成功</el-tag>
                                <el-tag type="error" v-else effect="dark" size="small">失败</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column align="center" label="访问时间" prop="created_at" width="185" sortable='custom'>
                            <template #default="scope">
                                <span>{{ $tool.dateFormat(scope.row.created_at) }}</span>
                            </template>
                        </el-table-column>
                    </DataTable>
                </el-card>
            </el-main>
        </div>
    </Layout>
</template>
<script>
import { useForm } from '@inertiajs/vue3'
import { pinyin } from 'pinyin-pro';
export default {
    props: {
        item: Object,
        profile: Object,
        options: Object,
        tab: {
            type: String,
            default: 'profile'
        }
    },
    data() {
        let validateIdCard = (rule, value, callback) => {
            let r1 = /(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/;
            if (!r1.test(value)) {
                callback(new Error("请输入正确的证件号码"));
            } else {
                callback();
            }
        };
        let validatePassword = (rule, value, callback) => {
            let r1 = null;
            if (this.item.is_super) {
                r1 = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*.])[\da-zA-Z~!@#$%^&*.]{15,}$/;
            } else {
                r1 = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*.])[\da-zA-Z~!@#$%^&*.]{6,}$/;
            }
            if (!r1.test(value)) {
                callback(new Error("密码必须是" + (this.item.is_super ? '15' : '6') + "位以上、必须含有字母、数字、特殊符号"));
            } else {
                callback();
            }
        };
        let validatePasswordConfirmation = (rule, value, callback) => {
            if (value != this.passwordForm.password) {
                callback(new Error("两次输入的密码不相符"));
            } else {
                callback();
            }
        };
        return {
            title: '个人信息',
            avatar: '',
            menu: [
                {
                    groupName: '基本信息',
                    list: [
                        {
                            icon: "el-icon-user",
                            title: '个人资料',
                            component: "profile"
                        },
                        {
                            icon: "el-icon-postcard",
                            title: '账号信息',
                            component: "account"
                        },
                        {
                            icon: "el-icon-lock",
                            title: '密码安全',
                            component: "password"
                        }
                    ]
                },
                {
                    groupName: '数据信息',
                    list: [
                        {
                            icon: "el-icon-clock",
                            title: '操作记录',
                            component: "log"
                        }
                    ]
                }
            ],
            profileForm: useForm({
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
            }),
            profileRules: {
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
            passwordForm: useForm({
                current_password: "",
                password: "",
                password_confirmation: ""
            }),
            passwordRules: {
                current_password: [
                    { required: true, message: '当前密码不能为空', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '新密码不能为空', trigger: 'blur' },
                    { validator: validatePassword }
                ],
                password_confirmation: [
                    { required: true, message: '重复密码不能为空', trigger: 'blur' },
                    { validator: validatePasswordConfirmation }
                ]
            },
            isSaving: false,
            page: this.tab,
            nextAction: ''
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.avatar = this.item.pinyin.substring(0, 1).toUpperCase()
            this.convertProfile()
        })
    },
    watch: {
        'profileForm.name': {
            handler(val) {
                let pinyinText = pinyin(val, { toneType: 'none', mode: 'surname', v: true, nonZh: 'removed' })
                var str = pinyinText.toLowerCase();
                var strArr = str.split(' ');
                var result = '';
                for (var i in strArr) {
                    result += strArr[i].substring(0, 1).toUpperCase() + strArr[i].substring(1) + ' ';
                }
                this.profileForm.pinyin = result;
            }
        }
    },
    methods: {
        convertProfile() {
            this.profileForm.name = this.profile.name
            this.profileForm.pinyin = this.profile.pinyin
            this.profileForm.gender = this.profile.gender
            this.profileForm.birth = this.profile.birth
            this.profileForm.nation = this.profile.nation
            this.profileForm.birthplace = this.profile.birthplace
            this.profileForm.address = this.profile.address
            this.profileForm.id_card = this.profile.id_card
            this.profileForm.educational = this.profile.educational
            this.profileForm.science = this.profile.science
            this.profileForm.emergency_contact = this.profile.emergency_contact
            this.profileForm.emergency_telephone = this.profile.emergency_telephone
            this.profileForm.skill_level = parseInt(this.profile.skill_level)
            this.profileForm.career_level = parseInt(this.profile.career_level)
            this.profileForm.attend_date = this.profile.attend_date
            this.profileForm.entry_date = this.profile.entry_date
        },
        openPage(item) {
            this.page = item.index
        },
        async profileSubmit() {
            var validate = await this.$refs.profileForm.validate().catch(() => { })
            if (!validate) return false;
            this.profileForm.post(this.$route('profile.index'), {
                onError: page => {
                    this.$tool.error(page)
                }
            })
        },
        async passwordSubmit() {
            var validate = await this.$refs.passwordForm.validate().catch(() => { })
            if (!validate) return false;
            this.passwordForm.post(this.$route('profile.password'))
        },
        checkPassword(callback) {
            this.$prompt('请输入登录密码', '验证密码', {
                confirmButtonText: '下一步',
                cancelButtonText: '取消',
                inputType: 'password',
            }).then(async ({ value }) => {
                const res = await this.$axios.post(this.$route('check.password'), {
                    password: value
                })
                if (res.code == this.$config.successCode) {
                    callback && this[callback]()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        onEditEmail() {
            this.$prompt('请输入您的邮箱地址', '修改邮箱', {
                confirmButtonText: '修改',
                cancelButtonText: '取消',
                inputPattern: /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/,
                inputErrorMessage: '请输入正确的邮箱地址'
            }).then(async ({ value }) => {
                const res1 = await this.$axios.post(this.$route('profile.account'), {
                    account: value,
                    type: 'email'
                })
                if (res1.code == this.$config.successCode) {
                    this.$message.success('修改邮箱地址成功')
                } else {
                    this.$message.error(res1.message)
                }
            }).catch(() => { });
        },
        onEditMobile() {

            this.$prompt('请输入您的手机号码', '修改手机号码', {
                confirmButtonText: '修改',
                cancelButtonText: '取消',
                inputPattern: /^1[3-9][0-9]{9}$/,
                inputErrorMessage: '请输入正确的手机号码'
            }).then(async ({ value }) => {
                const res1 = await this.$axios.post(this.$route('profile.account'), {
                    account: value,
                    type: 'mobile'
                })
                if (res1.code == this.$config.successCode) {
                    this.$message.success('修改手机号码成功')
                } else {
                    this.$message.error(res1.message)
                }
            }).catch(() => { });
        },
        onEditNumber() {
            this.$prompt('请输入您的员工号', '修改员工号', {
                confirmButtonText: '修改',
                cancelButtonText: '取消',
                inputValue: this.item.number,
            }).then(async ({ value }) => {
                const res = await this.$axios.post(this.$route('profile.account'), {
                    account: value,
                    type: 'number'
                })
                if (res.code == this.$config.successCode) {
                    this.$message.success('修改员工号成功')
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { });
        }
    }
}
</script>

<style lang="scss" scoped>
.profile {
    min-height: 650px;
    box-sizing: border-box;
    @extend .flexColumn;
    flex-direction: row;
    align-items: flex-start;

    :deep(.el-aside),
    :deep(.el-main) {
        height: 100%;
    }

    &-avatar {
        width: 100%;
        height: 160px;
        margin-bottom: 15px;
        @extend .flexColumn;
    }

    &-menu {
        height: 720px;
        width: 100%;

        :deep(.el-menu-item-group__title) {
            font-size: 16px;
        }
    }

    :deep(.el-card__header) {
        font-size: 20px;
    }

    &-subtitle {
        padding: 10px 15px;
        font-size: 16px;
        color: var(--el-text-color-secondary);
        display: inline-block;
        width: 100%;
    }

    &-list {
        width: 100%;
        display: flex;
        flex-direction: column;

        &-item {
            width: 100%;
            height: 46px;
            @extend .flexColumn;
            flex-direction: row;

            &-label {
                width: 100px;
                font-size: 16px;
            }

            &-desc {
                flex: 1;
                font-size: 16px;
                color: var(--el-text-color-secondary);
                @extend .flexColumn;
                flex-direction: row;
                justify-content: flex-end;
                align-items: center;

                &::before {
                    content: '';
                    height: 1px;
                    border-bottom: var(--el-text-color-disabled) 1px dashed;
                    flex: 1;
                    margin: 0 20px 0 40px;
                }

                :deep(.el-icon) {
                    font-size: 18px;
                    margin-left: 10px;
                    margin-top: 4px;
                }

            }
        }
    }
}
</style>