<template>
    <Layout>
        <el-alert title="单击单元格数字可编辑Outbound值" type="warning" />
        <TopNav></TopNav>
        <div class="page-block">
            <el-calendar ref="calendar" @input="changeDate" v-loading="loading" v-model="query.month">
                <template #header>
                    <el-button @click="selectDate('prev')" icon="el-icon-arrow-left-bold">
                        上一个月
                    </el-button>
                    <el-text tag="h2" size="large">{{ $tool.dateFormat(month, 'YYYY-MM') }}</el-text>
                    <el-button @click="selectDate('next')" icon="el-icon-arrow-right-bold">
                        下一个月
                    </el-button>
                </template>

                <template #date-cell="{ data }">
                    <span>{{ data.day.split('-').slice(1).join('-') }}</span>
                    <div class="page-block-tag" v-if="data.type == 'current-month'">
                        <span :class="{ active: getValue(data.day) > 0 }">{{ getValue(data.day)
                            }}</span>
                    </div>
                </template>
            </el-calendar>
        </div>
        <el-dialog title="每日发运量" v-model="editable" @closed="editable = false">
            <el-form :model="form" ref="form" label-position="top" :rules="rules" v-loading="saving" label-width="120px"
                @submit.native.prevent="onSubmit">

                <el-form-item label="日期" prop="daily">
                    <el-input v-model="form.daily" disabled></el-input>
                </el-form-item>
                <el-form-item prop="outbound">
                    <el-table border :data="eb_type">
                        <el-table-column label="机型" prop="name"></el-table-column>
                        <el-table-column label="发运量">
                            <template #default="scope">
                                <el-input type="number" min="0" v-model="form.outbound[scope.row.value]"
                                    placeholder="请输入每日发运量"></el-input>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="editable = false">取消</el-button>
                    <el-button type="primary" v-loading="saving" @click="onSubmit">确定</el-button>
                </div>
            </template>
        </el-dialog>
    </Layout>
</template>
<script>
import dayjs from "dayjs";
export default {
    props: {
        month: {
            type: String
        },
        items: {
            type: Object,
            default: {}
        },
        eb_type: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            loading: false,
            form: {
                daily: '',
                outbound: '',
            },
            query: {
                month: null
            },
            rules: {
                daily: [
                    { required: true, message: '请输入日期', trigger: 'blur' }
                ],
                outbound: [
                    { required: true, message: '请输入发运量', trigger: 'blur' }
                ]
            },
            editable: false,
            saving: false
        }
    },
    watch: {
        month(val) {
            this.query.month = new Date(val)
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.query.month = new Date(this.month)
        })
    },
    methods: {
        changeDate(e) {
            let date = dayjs(e)
            if (!this.loading) {
                if (date.format('YYYY-MM') != this.month) {
                    this.loading = true
                    this.query.month = date.format('YYYY-MM')
                    this.$ajax.visit(this.$route('vehicle.outbound', this.query), {
                        onFinish: () => {
                            this.loading = false
                        }
                    })
                } else {
                    this.form = {
                        daily: date.format('YYYY-MM-DD'),
                        outbound: this.getBound(date.format('YYYY-MM-DD'))
                    }
                    this.editable = true
                }
            }
        },
        getBound(val) {
            let item = this.items[val] ?? {};
            let bounds = {}

            this.eb_type.forEach(n => {
                bounds[n.value] = 0
            })
            if (Object(item).length != 0) {
                bounds = item
            }
            return bounds;
        },
        getValue(val) {
            let keys = Object.keys(this.items)
            let count = 0;
            if (keys.indexOf(val) === -1) { count = 0; } else {
                Object.keys(this.items[val]).forEach(n => {
                    count += this.items[val][n]
                })
            }
            return count
        },
        selectDate(val) {
            if (!this.loading) {
                this.loading = true
                let date = dayjs(this.query.month)
                this.query.month = date.add(val == 'next' ? 1 : -1, 'month').format('YYYY-MM')
                this.$ajax.visit(this.$route('vehicle.outbound', this.query), {
                    onFinish: () => {
                        this.loading = false
                    }
                })
            }
        },
        onSubmit() {
            if (!this.loading && !this.saving) {
                this.loading = true
                this.saving = true
                this.$ajax.post(this.$route('vehicle.outbound_update'), this.form, {
                    forceFormData: true,
                    onFinish: () => {
                        this.loading = false
                        this.saving = false
                        this.$ajax.reload();
                    },
                    onSuccess: () => {

                        this.editable = false
                    }
                })
            }
        },
    }
}
</script>

<style scoped lang="scss">
.page-block {
    :deep(.el-calendar) {
        --el-calendar-cell-width: 100px !important;

        .el-calendar-day {
            font-weight: 400;
            font-size: 16px;
        }
    }

    &-tag {
        width: 100%;
        height: 50px;
        margin-top: 10px;
        @extend .flexColumn;
        flex-direction: row;
        align-items: flex-end;
        justify-content: flex-end;

        span {
            font-size: 22px;
            color: var(--el-text-color);

            &.active {
                color: var(--el-color-primary);
            }
        }
    }
}
</style>