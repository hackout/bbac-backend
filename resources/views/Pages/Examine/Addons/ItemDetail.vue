<template>
    <el-drawer :title="commit.name + '考核项详情'" v-model="visitable" size="calc(100vw - 400px)"
        @closed="$emit('closed')">
        <el-descriptions :column="1" v-if="type == 'inline'" border class="detail-box">
            <el-descriptions-item label="考核类型"><span>{{ $status('examine_item_type',item.type) }}</span></el-descriptions-item>
            <el-descriptions-item label="工位"><span>{{ item.station }}</span></el-descriptions-item>
            <el-descriptions-item v-if="item.type != 4 && item.type != 5" label="工位2"><span>{{ item.sub_station
                    }}</span></el-descriptions-item>
            <template v-if="item.type == 1">
                <el-descriptions-item label="螺栓编号">
                    <span>{{ item.bolt_number }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="描述(英文)">
                    <span>{{ item.content_en }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="描述(中文)">
                    <span>{{ item.content_zh }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="螺栓数量">
                    <span>{{ item.number }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="螺栓型号">
                    <span>{{ $status('bolt_model', item.bolt_model) }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="螺栓种类">
                    <span>{{ $status('bolt_type', item.bolt_type) }}</span>
                </el-descriptions-item>
            </template>
            <template v-else-if="item.type > 3">
                <el-descriptions-item label="测量内容(中文)"><span>{{ item.content_zh }}</span></el-descriptions-item>
                <el-descriptions-item label="测量内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            </template>
            <template v-else>
                <el-descriptions-item label="检查内容(中文)"><span>{{ item.content_zh }}</span></el-descriptions-item>
                <el-descriptions-item label="检查内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            </template>
            <template v-if="item.type > 1">
                <el-descriptions-item label="检查标准(中文)"><span>{{ item.standard_zh }}</span></el-descriptions-item>
                <el-descriptions-item label="检查标准(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            </template>
            <el-descriptions-item v-if="item.type == 4" label="墨水型号">
                <span>{{ item.gluing }}</span>
            </el-descriptions-item>
            <el-descriptions-item v-if="item.type > 1" label="检查数量">
                <span>{{ item.number }}</span>
            </el-descriptions-item>
            <el-descriptions-item v-if="item.type != 4 && item.type != 5" label="特殊特性">
                <span>{{ $status('special', item.special) }}</span>
            </el-descriptions-item>

            <el-descriptions-item v-if="item.type == 1" label="放行状态">
                <span>{{ $status('bolt_status', item.bolt_status) }}</span>
            </el-descriptions-item>
            <template v-if="item.type == 1 || item.type == 2">
                <el-descriptions-item label="测量下限"><span>{{ item.lower_limit }}</span></el-descriptions-item>
                <el-descriptions-item label="测量上限"><span>{{ item.upper_limit }}</span></el-descriptions-item>
                <el-descriptions-item label="测量单位"><span>{{ item.unit }}</span></el-descriptions-item>
            </template>
            <el-descriptions-item label="实际测量值(中)"><span>{{ item.name_zh }}</span></el-descriptions-item>
            <el-descriptions-item label="实际测量值(英)"><span>{{ item.name_en }}</span></el-descriptions-item>
            <el-descriptions-item label="图示">
                <el-upload :file-list="item.thumbnails" list-type="picture-card" :on-preview="handlePictureCardPreview"
                    disabled>
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-descriptions-item>
        </el-descriptions>
        <el-descriptions :column="1" v-if="type == 'product'" border class="detail-box">
            <el-descriptions-item label="考核类型"><span>{{ $status('examine_item_type',item.type) }}</span></el-descriptions-item>
            <el-descriptions-item label="工作序号"><span>{{ item.sort_order }}</span></el-descriptions-item>
            <el-descriptions-item label="工作内容(中文)"><span>{{ item.content_zh }}</span></el-descriptions-item>
            <el-descriptions-item label="工作内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            <el-descriptions-item label="操作描述(中文)"><span>{{ item.standard_zh }}</span></el-descriptions-item>
            <el-descriptions-item label="操作描述(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            <el-descriptions-item label="拧紧螺距要求"><span>{{ item.blot_close }}</span></el-descriptions-item>
            <el-descriptions-item label="螺栓数量"><span>{{ item.number }}</span></el-descriptions-item>
            <el-descriptions-item label="测量项(中文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name_zh
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="测量项(英文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="目测检查(中文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye_zh
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="目测检查(英文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="测量下限"><span>{{ item.lower_limit }}</span></el-descriptions-item>
            <el-descriptions-item label="测量上限"><span>{{ item.upper_limit }}</span></el-descriptions-item>
            <el-descriptions-item label="测量单位"><span>{{ item.unit }}</span></el-descriptions-item>
            <el-descriptions-item label="是否扫码"><span>{{ item.is_scan ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label="是否拍照"><span>{{ item.is_camera ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label="零件编号"><span>{{ $status('torque',item.part_number) }}</span></el-descriptions-item>
            <el-descriptions-item label="进度"><span>{{ item.process }}</span></el-descriptions-item>

            <el-descriptions-item label="图示">
                <el-upload :file-list="item.thumbnails" list-type="picture-card" :on-preview="handlePictureCardPreview"
                    disabled>
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-descriptions-item>
        </el-descriptions>
        <el-descriptions v-if="type == 'service'" :column="1" border class="detail-box">
            <el-descriptions-item label="考核类型"><span>{{ $status('examine_item_type',item.type) }}</span></el-descriptions-item>
            <el-descriptions-item label="工作序号"><span>{{ item.sort_order }}</span></el-descriptions-item>
            <el-descriptions-item label="工作内容(中文)"><span>{{ item.content_zh }}</span></el-descriptions-item>
            <el-descriptions-item label="工作内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            <el-descriptions-item label="操作描述(中文)"><span>{{ item.standard_zh }}</span></el-descriptions-item>
            <el-descriptions-item label="操作描述(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            <el-descriptions-item label="测量项(中文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name_zh
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="测量项(英文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="目测检查(中文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye_zh
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="目测检查(英文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label="测量下限"><span>{{ item.lower_limit }}</span></el-descriptions-item>
            <el-descriptions-item label="测量上限"><span>{{ item.upper_limit }}</span></el-descriptions-item>
            <el-descriptions-item label="测量单位"><span>{{ item.unit }}</span></el-descriptions-item>
            <el-descriptions-item label="是否扫码"><span>{{ item.is_scan ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label="是否拍照"><span>{{ item.is_camera ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label="零件编号"><span>{{ $status('torque',item.part_number) }}</span></el-descriptions-item>
            <el-descriptions-item label="进度"><span>{{ item.process }}</span></el-descriptions-item>

            <el-descriptions-item label="图示">
                <el-upload :file-list="item.thumbnails" list-type="picture-card" :on-preview="handlePictureCardPreview"
                    disabled>
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-descriptions-item>
        </el-descriptions>
        <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :url-list="viewerList" teleported />
        <template #footer>
            <div class="drawer-footer">
                <el-button @click="visitable = false">关闭</el-button>
            </div>
        </template>
    </el-drawer>
</template>

<script>
export default {
    props: {
        special: {
            type: Array,
            default: []
        },
        bolt_model: {
            type: Array,
            default: []
        },
        bolt_type: {
            type: Array,
            default: []
        },
        bolt_status: {
            type: Array,
            default: []
        },
        torque: {
            type: Array,
            default: []
        },
        examine_item_type: {
            type: Array,
            default: []
        },
    },
    emits: ['closed'],
    data() {
        return {
            visitable: false,
            showViewer: false,
            item: {},
            commit: {},
            viewerList: [],
            type: 'inline'
        }
    },
    methods: {
        open(type, item, commit) {
            this.type = type
            this.item = item
            this.commit = commit
            this.viewerList = this.item.thumbnails ? this.item.thumbnails.map(n => n.url) : []
            this.visitable = true
        },
        handlePictureCardPreview() {
            this.showViewer = true
        }
    }
}
</script>

<style lang="scss">
.detail-box .el-upload {
    display: none !important;
}
</style>