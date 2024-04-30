<template>
    <el-drawer :title="commit.name + '考核项详情'" v-model="visitable" size="calc(100vw - 400px)"
        @closed="$emit('closed')">
        <el-descriptions :column="1" border class="detail-box">
            <el-descriptions-item label-class-name="detail-box-label" label="检查类型"><span>{{ $status('examine_inline_item_type',item.type) }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工位"><span>{{ item.station }}</span></el-descriptions-item>
            <template v-if="item.type == 1">
                <el-descriptions-item label-class-name="detail-box-label" label="螺栓编号">
                    <span>{{ item.bolt_number }}</span>
                </el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="描述(英文)">
                    <span>{{ item.content_en }}</span>
                </el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="描述(中文)">
                    <span>{{ item.content }}</span>
                </el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="螺栓数量">
                    <span>{{ item.number }}</span>
                </el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="螺栓型号">
                    <span>{{ $status('bolt_model', item.bolt_model) }}</span>
                </el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="螺栓种类">
                    <span>{{ $status('bolt_type', item.bolt_type) }}</span>
                </el-descriptions-item>
            </template>
            <template v-else-if="item.type > 3">
                <el-descriptions-item label-class-name="detail-box-label" label="测量内容(中文)"><span>{{ item.content }}</span></el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="测量内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            </template>
            <template v-else>
                <el-descriptions-item label-class-name="detail-box-label" label="检查内容(中文)"><span>{{ item.content }}</span></el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="检查内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            </template>
            <template v-if="item.type > 1">
                <el-descriptions-item label-class-name="detail-box-label" label="检查标准(中文)"><span>{{ item.standard }}</span></el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="检查标准(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            </template>
            <el-descriptions-item label-class-name="detail-box-label" v-if="item.type == 4" label="墨水型号">
                <span>{{ item.gluing }}</span>
            </el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" v-if="item.type > 1" label="检查数量">
                <span>{{ item.number }}</span>
            </el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" v-if="item.type != 4 && item.type != 5" label="特殊特性">
                <span>{{ $status('special', item.special) }}</span>
            </el-descriptions-item>

            <el-descriptions-item label-class-name="detail-box-label" v-if="item.type == 1" label="放行状态">
                <span>{{ $status('bolt_status', item.bolt_status) }}</span>
            </el-descriptions-item>
            <template v-if="item.type == 1 || item.type == 2">
                <el-descriptions-item label-class-name="detail-box-label" label="测量下限"><span>{{ item.lower_limit }}</span></el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="测量上限"><span>{{ item.upper_limit }}</span></el-descriptions-item>
                <el-descriptions-item label-class-name="detail-box-label" label="测量单位"><span>{{ item.unit }}</span></el-descriptions-item>
            </template>
            <el-descriptions-item label-class-name="detail-box-label" label="实际测量值"><span>{{ item.name }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="图示">
                <el-upload :file-list="item.thumbnails" list-type="picture-card" :on-preview="handlePictureCardPreview"
                    disabled>
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-descriptions-item>
        </el-descriptions>
        <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex" :url-list="viewerList" teleported />
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
        bolt_status: {
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
        examine_inline_item_type: {
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
            viewerIndex: 0
        }
    },
    methods: {
        open(item, commit) {
            this.item = item
            this.commit = commit
            this.viewerList = this.item.thumbnails ? this.item.thumbnails.map(n => n.url) : []
            this.visitable = true
        },
        handlePictureCardPreview() {
            this.item.thumbnails.forEach((n,i)=>{
                if(n.uuid == e.uuid)
                {
                    this.viewerIndex = i
                }
            })
            this.showViewer = true
        }
    }
}
</script>

<style lang="scss">
.detail-box-label{
    width: 150px;
    font-weight: 200 !important;
    text-align: right !important;
}
.detail-box .el-upload {
    display: none !important;
}
</style>