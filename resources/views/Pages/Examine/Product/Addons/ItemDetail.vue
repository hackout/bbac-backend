<template>
    <el-drawer :title="commit.name + '考核项详情'" v-model="visitable" size="calc(100vw - 400px)" @closed="$emit('closed')">
        <el-descriptions :column="1" border class="detail-box">
            <el-descriptions-item label-class-name="detail-box-label" label="考核类型"><span>{{ $status('examine_product_item_type', item.type)
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作序号"><span>{{ item.sort_order }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作内容"><span>{{ item.content }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="操作描述"><span>{{ item.standard }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="操作描述(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="拧紧扭矩要求"><span>{{ item.torque }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="螺栓数量"><span>{{ item.number }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="测量项(中文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="测量项(英文)" v-if="item.type == 1 || item.type == 3"><span>{{ item.name_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="目测检查(中文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="目测检查(英文)" v-if="item.type == 2 || item.type == 3"><span>{{ item.eye_en
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="测量下限"><span>{{ item.lower_limit }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="测量上限"><span>{{ item.upper_limit }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="测量单位"><span>{{ item.unit }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="是否扫码"><span>{{ item.is_scan ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="扫码说明"><span>{{ item.scan }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="是否拍照"><span>{{ item.is_camera ? 'YES' : 'NO' }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="拍照寿命"><span>{{ item.camera }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="零件编号"><span>{{ $status('parts', item.part_id) }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="进度"><span>{{ item.process }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="记录说明"><span>{{ item.record }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="图示">
                <el-upload :file-list="item.thumbnails" list-type="picture-card" :on-preview="handlePictureCardPreview"
                    disabled>
                    <el-icon-plus>
                    </el-icon-plus>
                </el-upload>
            </el-descriptions-item>
        </el-descriptions>
        <el-image-viewer v-if="showViewer" @close="showViewer = false" infinite :initial-index="viewerIndex"
            :url-list="viewerList" teleported />
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
        parts: {
            type: Array,
            default: []
        },
        examine_product_item_type: {
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
        handlePictureCardPreview(e) {
            this.item.thumbnails.forEach((n, i) => {
                if (n.uuid == e.uuid) {
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