<template>
    <el-drawer :title="commit.name + '考核项详情'" v-model="visitable" size="calc(100vw - 400px)" @closed="$emit('closed')">
        <el-descriptions :column="1" border class="detail-box">
            <el-descriptions-item label-class-name="detail-box-label" label="考核类型"><span>{{ $status('examine_vehicle_item_type', item.type)
                    }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作序号"><span>{{ item.sort_order }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作内容(中文)"><span>{{ item.content }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="工作内容(英文)"><span>{{ item.content_en }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="检查标准(中文)"><span>{{ item.standard }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="检查标准(英文)"><span>{{ item.standard_en }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="其他要求(中文)"><span>{{ item.other }}</span></el-descriptions-item>
            <el-descriptions-item label-class-name="detail-box-label" label="其他要求(英文)"><span>{{ item.other_en }}</span></el-descriptions-item>
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
        examine_vehicle_item_type: {
            type: Array,
            default: []
        },
    },
    emits: ['closed'],
    data() {
        return {
            visitable: false,
            showViewer: false,
            viewerIndex: 0,
            item: {},
            commit: {},
            viewerList: [],
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

<style lang="scss" scope>
.detail-box-label{
    width: 150px;
    font-weight: 200 !important;
    text-align: right !important;
}
.detail-box .el-upload {
    display: none !important;
}
</style>