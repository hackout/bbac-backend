<template>
    <Layout>
        <div class="page-block file">
            <div class="page-search">
                <div class="page-search-button">
                    <el-button type="primary" @click="tableClick('create')" icon="el-icon-plus">新增文件夹</el-button>
                    <el-upload :action="$route('file.upload', { path: query.path })" :key="query.path" id="importUpload"
                        ref="importUpload" :on-success="refreshData" class="page-search-buttons-upload" :limit="9"
                        :show-file-list="false" multiple :headers="uploadHeaders">
                        <el-button icon="el-icon-upload" type="primary">上传文件</el-button>
                    </el-upload>
                    <el-button :type="`${query.hidden == 1 ? 'primary' : 'default'}`" @click="setHidden"
                        :icon="`el-icon-${query.hidden == 1 ? 'view' : 'hide'}`">隐藏文件</el-button>
                    <el-tooltip content="双击进入文件夹或修改文件名<br />右键更多操作选项" placement="right-start" raw-content
                        effect="light">
                        <el-button link icon="el-icon-question-filled"></el-button>
                    </el-tooltip>
                </div>
                <div class="page-search-form">
                    <el-button-group style="margin-left:12px;">
                        <el-button :type="`${mode == 'list' ? 'primary' : 'default'}`" icon="el-icon-list"
                            @click="setMode('list')"></el-button>
                        <el-button :type="`${mode == 'grid' ? 'primary' : 'default'}`" icon="el-icon-grid"
                            @click="setMode('grid')"></el-button>
                    </el-button-group>
                </div>
            </div>
            <el-page-header @back="tableClick('prev')">
                <template #content>
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item v-for="(breadcrumb, index) in breadcrumbs" :key="index"
                            @click="goDirectory(breadcrumb.path)">
                            {{ breadcrumb.path == '/' ? '根目录' : breadcrumb.name }}
                        </el-breadcrumb-item>
                    </el-breadcrumb>
                </template>
            </el-page-header>
            <div class="file-table" @contextmenu.prevent.stop="openContextMenu">
                <el-scrollbar>
                    <div class="file-table-box" v-loading="loading">
                        <el-row :gutter="20" v-if="items.length > 0 && mode == 'grid'">
                            <el-col :xs="12" :sm="8" :md="6" :lg="4" :xl="3" v-for="(item, index) in items" :key="index"
                                @contextmenu.prevent.stop="openContextMenuItem(item, $event)">
                                <div class="file-table-item" :title="item.name"
                                    :class="`${item.hide == 1 ? 'hide' : ''} ${item.type == 'directory' ? 'folder' : item.type}`"
                                    @dblclick="enterDirectory(item)">
                                    <div class="file-table-item-icon">
                                        <component :is="icons[item.type]"></component>
                                    </div>
                                    <div class="file-table-item-text" v-if="showEdit && item.id == currentItem.id"
                                        ref="inputName">
                                        <el-input size="small" v-model="currentItem.name" @keyup.enter="saveFileName">
                                            <template #append>
                                                <el-button size="small" @click="saveFileName" type="success"
                                                    icon="el-icon-check"></el-button>
                                                <el-button size="small" type="danger" @click="closeEditName"
                                                    icon="el-icon-close"></el-button>
                                            </template>
                                        </el-input>
                                    </div>
                                    <div class="file-table-item-text" style="padding: 0 5px;text-align: center;" v-else>
                                        <el-text truncated line-clamp="1" @dblclick.prevent.stop="showEditName(item)">
                                            {{ item.name }}</el-text>
                                    </div>
                                    <div v-if="showMultiple" class="file-table-item-checkbox">
                                        <el-checkbox @change="changeMultiple(item)"
                                            :checked="multiples.indexOf(item.id) > -1" size="large"></el-checkbox>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" v-else-if="items.length > 0 && mode == 'list'">
                            <el-col :span="24" class="table-list-head">
                                <el-row>
                                    <el-col :span="10">
                                        文件(夹)名
                                    </el-col>
                                    <el-col :span="4">
                                        类型
                                    </el-col>
                                    <el-col :span="4">
                                        大小
                                    </el-col>
                                    <el-col :span="6">
                                        最后修改
                                    </el-col>
                                </el-row>
                            </el-col>
                            <el-col :span="24" v-for="(item, index) in items" :key="index"
                                @contextmenu.prevent.stop="openContextMenuItem(item, $event)" class="table-list-item"
                                :class="`${item.hide == 1 ? 'hide' : ''} ${item.type == 'directory' ? 'folder' : item.type}`">
                                <el-row>
                                    <el-col v-if="showMultiple" :span="1">
                                        <el-checkbox @change="changeMultiple(item)"
                                            :checked="multiples.indexOf(item.id) > -1" size="large"></el-checkbox>
                                    </el-col>
                                    <el-col :span="1">
                                        <div class="file-table-item-icon">
                                            <component :is="icons[item.type]"></component>
                                        </div>
                                    </el-col>
                                    <el-col :span="showMultiple ? 8 : 9">
                                        <div class="file-table-item-text" v-if="showEdit && item.id == currentItem.id"
                                            ref="inputName">
                                            <el-input size="small" v-model="currentItem.name"
                                                @keyup.enter="saveFileName">
                                                <template #append>
                                                    <el-button size="small" @click="saveFileName" type="success"
                                                        icon="el-icon-check"></el-button>
                                                    <el-button size="small" type="danger" @click="closeEditName"
                                                        icon="el-icon-close"></el-button>
                                                </template>
                                            </el-input>
                                        </div>
                                        <div class="file-table-item-text" v-else>
                                            <el-text truncated line-clamp="1"
                                                @dblclick.prevent.stop="showEditName(item)">
                                                {{ item.name }}
                                            </el-text>
                                        </div>
                                    </el-col>
                                    <el-col :span="4">
                                        <el-text truncated line-clamp="1">{{ item.type }}</el-text>
                                    </el-col>
                                    <el-col :span="4">
                                        {{ $tool.byteString(item.size) }}
                                    </el-col>
                                    <el-col :span="6">
                                        {{ $tool.dateFormat(item.lastModified) }}
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>
                        <el-empty :image-size="200" description="暂无文件" v-else />
                    </div>
                </el-scrollbar>
            </div>
            <div class="file-menu" ref="tableMenu" v-show="showTableMenu" :style="tableStyle">
                <div class="file-menu-group">
                    <div class="file-menu-item grid" @click="tableClick('prev')"
                        :class="{ disabled: query.path == '/' }">
                        <el-icon-arrow-left></el-icon-arrow-left>
                    </div>
                    <div class="file-menu-item grid" @click="tableClick('next')" :class="canNextClass">
                        <el-icon-arrow-right></el-icon-arrow-right>
                    </div>
                    <div class="file-menu-item grid" @click="tableClick('refresh')">
                        <el-icon-refresh></el-icon-refresh>
                    </div>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" @click="tableClick('create')">
                    <span>新建文件夹</span>
                </div>
                <div class="file-menu-item" @click="tableClick('upload')">
                    <span>上传文件</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" :class="{ disabled: showMultiple }"
                    @click="showMultiple = true, showTableMenu = false">
                    <span>选择</span>
                </div>
                <div class="file-menu-item" :class="{ disabled: !showMultiple }"
                    @click="showMultiple = false, showTableMenu = false">
                    <span>取消</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" :class="{ disabled: multiples.length == 0 }" @click="multipleDelete">
                    <span>删除</span>
                </div>
                <div class="file-menu-item" :class="{ disabled: multiples.length == 0 }" @click="multipleMove">
                    <span>移动到..</span>
                </div>
            </div>
            <div class="file-menu" ref="itemMenu" v-show="showItemMenu" :style="itemStyle">
                <div class="file-menu-item" :class="{ disabled: currentItem.type != 'directory' }"
                    @click="enterFile(currentItem)">
                    <span>打开</span>
                </div>
                <div class="file-menu-item" :class="{ disabled: !canViewer(currentItem) }"
                    @click="viewItem(currentItem)">
                    <span>预览</span>
                </div>
                <div class="file-menu-item" :class="{ disabled: currentItem.type == 'directory' }"
                    @click="downloadItem(currentItem)">
                    <span>下载</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" @click="showEditName(currentItem)">
                    <span>重命名</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" @click="setVisit(currentItem, 1)"
                    :class="{ disabled: currentItem.hide == 1 }">
                    <span>隐藏</span>
                </div>
                <div class="file-menu-item" @click="setVisit(currentItem, 0)"
                    :class="{ disabled: currentItem.hide == 0 }">
                    <span>显示</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" @click="deleteItem(currentItem)">
                    <span>删除</span>
                </div>
                <div class="file-menu-item" @click="moveItem(currentItem)">
                    <span>移动到..</span>
                </div>
                <div class="file-menu-divider"></div>
                <div class="file-menu-item" @click="tableClick('attribute', $event)">属性</div>
            </div>
            <div class="file-attribute" ref="itemAttribute" v-show="showAttribute" :style="itemStyle">
                <div class="file-attribute-title">
                    <span>{{ currentItem.name }}的属性</span>
                    <span @click="closeAttribute">
                        <el-icon-close></el-icon-close>
                    </span>
                </div>
                <div class="file-attribute-list">
                    <div class="file-attribute-list-item input">
                        <span class="file-attribute-list-item-icon"
                            :class="`${currentItem.type == 'directory' ? 'folder' : currentItem.type}`">
                            <component :is="icons[currentItem.type]"></component>
                        </span>
                        <div class="file-attribute-list-item-input">
                            <el-input v-model="attributeForm.name" placeholder="请输入文件(夹)名"></el-input>
                        </div>
                    </div>
                    <div class="file-attribute-list-divider"></div>
                    <div class="file-attribute-list-item" v-if="attributeForm.type != 'directory'">
                        <span class="file-attribute-list-item-label">文件类型:</span>
                        <span class="file-attribute-list-item-text">{{ attributeForm.mimeType }}</span>
                    </div>
                    <div class="file-attribute-list-item">
                        <span class="file-attribute-list-item-label">打开方式:</span>
                        <span class="file-attribute-list-item-text">{{ labels[attributeForm.type] }}</span>
                    </div>
                    <div class="file-attribute-list-divider"></div>
                    <div class="file-attribute-list-item">
                        <span class="file-attribute-list-item-label">位置:</span>
                        <span class="file-attribute-list-item-text">{{ attributeForm.directory }}</span>
                    </div>
                    <div class="file-attribute-list-item">
                        <span class="file-attribute-list-item-label">大小:</span>
                        <span class="file-attribute-list-item-text">{{ $tool.byteString(attributeForm.size) }}</span>
                    </div>
                    <div class="file-attribute-list-item" v-if="attributeForm.width > 0">
                        <span class="file-attribute-list-item-label">分辨率:</span>
                        <span class="file-attribute-list-item-text">{{ attributeForm.width }} x {{ attributeForm.height
                            }}</span>
                    </div>
                    <div class="file-attribute-list-item" v-if="attributeForm.duration > 0">
                        <span class="file-attribute-list-item-label">时长:</span>
                        <span class="file-attribute-list-item-text">{{ $tool.splitTime(attributeForm.duration) }}</span>
                    </div>
                    <div class="file-attribute-list-item" v-if="attributeForm.type == 'video'">
                        <span class="file-attribute-list-item-label">帧率:</span>
                        <span class="file-attribute-list-item-text">{{ Math.ceil(attributeForm.frame /
                            attributeForm.duration)
                            }}帧/秒</span>
                    </div>
                    <div class="file-attribute-list-item"
                        v-if="attributeForm.type == 'pdf' || attributeForm.type == 'ppt'">
                        <span class="file-attribute-list-item-label">页数:</span>
                        <span class="file-attribute-list-item-text">{{ attributeForm.frame }}页</span>
                    </div>
                    <div class="file-attribute-list-item">
                        <span class="file-attribute-list-item-label">最后修改:</span>
                        <span class="file-attribute-list-item-text">{{ $tool.dateFormat(attributeForm.lastModified)
                            }}</span>
                    </div>
                    <div class="file-attribute-list-item">
                        <span class="file-attribute-list-item-label">属性:</span>
                        <div class="file-attribute-list-item-text">
                            <el-radio-group v-model="attributeForm.hide">
                                <el-radio :label="0">显示</el-radio>
                                <el-radio :label="1">隐藏</el-radio>
                            </el-radio-group>
                        </div>
                    </div>
                </div>
                <div class="file-attribute-bottom">
                    <el-button @click="closeAttribute">关闭</el-button>
                    <el-button @click="saveAttribute">应用</el-button>
                </div>
            </div>
        </div>
        <el-dialog v-model="showTarget" width="400px" title="请选择移动到" @close="showTarget = false">
            <el-form>
                <el-form-item label="目标目录" prop="target">
                    <el-select style="width:100%;" v-model="targetPath" placeholder="请选择要移动到的路径">
                        <el-option value="/" label="根目录"></el-option>
                        <template v-for="(item, index) in items">
                            <el-option v-if="item.type == 'directory'" :value="item.path" :label="item.path"
                                :key="index"></el-option>
                        </template>
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showTarget = false">取消</el-button>
                <el-button type="primary" @click="multipleMoveTo">确定</el-button>
            </template>
        </el-dialog>
        <el-dialog v-if="currentItem.type != 'image'" v-model="showViewer" title="在线预览" @close="closeViewer">
            <div class="viewer" :class="{ audio: currentItem.type == 'music' }">
                <video v-if="currentItem.type == 'video'" ref="videoDialog" autoplay :src="viewerSource"
                    controls></video>
                <audio v-if="currentItem.type == 'music'" ref="musicDialog" autoplay :src="viewerSource"
                    controls></audio>
            </div>
        </el-dialog>
        <el-image-viewer v-if="showViewer && currentItem.type == 'image'" @close="showViewer = false" infinite
            :url-list="[viewerSource]" />
    </Layout>
</template>

<script>

export default {
    components: {
    },
    props: {

    },
    data() {
        return {
            uploadHeaders: {
                'X-XSRF-TOKEN': this.$tool.cookies.get('XSRF-TOKEN')
            },
            mode: 'grid',
            query: {
                hidden: 0,
                path: '/'
            },
            loading: false,
            showTableMenu: false,
            tableStyle: {
                left: 0,
                top: 0
            },
            showItemMenu: false,
            itemStyle: {
                left: 0,
                top: 0
            },
            items: [],
            showEdit: false,
            currentItem: {
                id: ''
            },
            showViewer: false,
            viewerSource: '',
            viewerContent: '',
            icons: {
                directory: 'bbac-icon-folder',
                file: 'bbac-icon-file',
                excel: 'bbac-icon-file-excel',
                image: 'bbac-icon-file-image',
                pdf: 'bbac-icon-file-pdf',
                ppt: 'bbac-icon-file-ppt',
                txt: 'bbac-icon-file-txt',
                video: 'bbac-icon-file-video',
                unknown: 'bbac-icon-file-unknown',
                word: 'bbac-icon-file-word',
                zip: 'bbac-icon-file-zip',
                music: 'bbac-icon-file-music',
                other: 'bbac-icon-file-other'
            },
            labels: {
                directory: '文件夹',
                file: '应用程式',
                excel: 'Excel',
                image: '图片',
                pdf: 'PDF',
                ppt: 'PPT',
                txt: '文本文档',
                video: '视频',
                unknown: '未知',
                word: 'Word',
                zip: '压缩包',
                music: '音频',
                other: '其他'
            },
            showAttribute: false,
            attributeForm: {
                id: ''
            },
            showMultiple: false,
            multiples: [],
            targetPath: '',
            showTarget: false
        }
    },
    computed: {
        breadcrumbs() {
            let paths = this.query.path == '/' ? [''] : this.query.path.split('/')
            let current = '/'
            var res = []
            paths.forEach((n, index) => {
                current += n
                res.push({ name: n, path: current })
                if (index > 0) {
                    current += '/'
                }
            })
            return res
        },
        canNextClass() {
            let ff = this.items.filter(n => n.type == 'directory')
            return ff.length > 0 ? 'disabled' : ''
        }
    },
    mounted() {
        this.$nextTick(() => {
            document.addEventListener('click', this.handleClickOutside, true);
            this.refreshData()
        })
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside, true);
    },
    methods: {
        changeMultiple(item) {
            let _has = this.multiples.filter(n => n == item.id)
            if (_has.length == 0) {
                this.multiples.push(item.id)
            } else {
                this.multiples = this.multiples.filter(n => n != item.id)
            }
        },
        closeViewer() {
            this.$refs.videoDialog && this.$refs.videoDialog.pause()
            this.$refs.musicDialog && this.$refs.musicDialog.pause()
            this.showViewer = false
        },
        canViewer(item) {
            if (!item || item.id == '') return false;
            if (['image', 'video', 'music'].indexOf(item.type) > -1) {
                return true;
            }
            return false;
        },
        viewItem(item) {
            if (this.canViewer(item)) {
                this.currentItem = item
                this.viewerSource = this.$route('file.view', { id: item.id, path: item.directory })
                this.showViewer = true
                this.showItemMenu = false;
            }
        },
        downloadItem(item) {
            if (item.type != 'directory') {
                this.$goTo('file.download', { id: item.id, path: item.path })
            }
            this.showItemMenu = false;
        },
        deleteItem(item) {
            this.$confirm('确定删除该文件?', '操作提示').then(async () => {
                let res = await this.$axios.put(this.$route('file.delete', { id: item.id }), { path: item.directory })
                this.showItemMenu = false;
                if (res.code == this.$config.successCode) {
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }).catch(() => { })
        },
        moveItem(item) {
            this.multiples = [item.id]
            this.showTarget = true
        },
        multipleMove() {
            if (this.multiples.length > 0) {
                this.showTarget = true
                this.showItemMenu = false;
                this.showTableMenu = false;
            }
        },
        multipleDelete() {
            if (this.multiples.length > 0) {
                this.$confirm('确定删除所选文件?', '操作提示').then(async () => {
                    let res = await this.$axios.post(this.$route('file.batch_delete'), { path: this.query.path, ids: this.multiples })
                    this.showTarget = false;
                    if (res.code == this.$config.successCode) {
                        this.refreshData()
                    } else {
                        this.$message.error(res.message)
                    }
                }).catch(() => { })
            }
        },
        async multipleMoveTo() {
            if (this.multiples.length > 0) {

                let res = await this.$axios.post(this.$route('file.batch_move'), { path: this.query.path, target: this.targetPath, ids: this.multiples })
                this.showTarget = false;
                if (res.code == this.$config.successCode) {
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }
        },
        async setVisit(item, val) {
            if ((item.hide == 0 && val == 1) || (item.hide == 1 && val == 0)) {
                let res = await this.$axios.put(this.$route('file.update', { id: item.id }), { visit: val, path: item.directory })
                this.showItemMenu = false;
                if (res.code == this.$config.successCode) {
                    this.refreshData()
                } else {
                    this.$message.error(res.message)
                }
            }
        },
        tableClick(command, e) {
            switch (command) {
                case 'refresh':
                    this.refreshData()
                    break;
                case 'prev':
                    let splits = this.query.path.split('/')
                    if (splits.length > 1) {
                        this.query.path = splits.slice(0, splits.length - 1).join('/')
                        if (this.query.path.length == 0) this.query.path = '/'
                        this.refreshData()
                    }
                    break;
                case 'next':
                    let ff = this.items.filter(n => n.type == 'directory')
                    if (ff.length > 0) {
                        this.query.path = ff[0].path
                        this.refreshData()
                    }
                    break;
                case 'create':
                    this.newFolder()
                    break;
                case 'upload':
                    let uploadButton = document.querySelector('#importUpload button')
                    if (uploadButton) {
                        uploadButton.click();
                    }
                    break;
                case 'attribute':
                    this.showAttribute = true
                    this.attributeForm = this.$tool.objCopy(this.currentItem)
                    let minTop = document.body.clientHeight - 550
                    if (this.currentItem.type == 'video') {
                        minTop = document.body.clientHeight - 650;
                    }
                    let minLeft = document.body.clientWidth - 450;
                    this.itemStyle = {
                        left: (minLeft < e.x ? minLeft : e.x) + 'px',
                        top: (minTop < e.y ? minTop : e.y) + 'px'
                    }
                    break;
            }
            this.showTableMenu = false
        },
        async newFolder() {
            var res = await this.$axios.post(this.$route('file.create'), this.query)
            if (res.code == this.$config.successCode) {
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        itemClick(command) {
            switch (command) {
                case 'refresh':
                    this.refreshData()
                    break;
            }
            this.showItemMenu = false
        },
        handleClickOutside(e) {
            if (!this.$refs.tableMenu.contains(e.target)) {
                this.showTableMenu = false
            }
            if (!this.$refs.itemMenu.contains(e.target)) {
                this.showItemMenu = false
            }
            if (!this.$refs.itemAttribute.contains(e.target)) {
                this.showAttribute = false
            }
            if (this.showEdit && !this.$refs.inputName[0].contains(e.target)) {
                this.closeEditName()
            }
        },
        setHidden() {
            this.query.hidden = this.query.hidden == 0 ? 1 : 0
            this.refreshData()
        },
        setMode(val) {
            this.mode = val
        },
        async refreshData() {
            this.loading = true
            var res = await this.$axios.get(this.$route('file.list'), this.query)
            this.loading = false
            this.showTableMenu = false
            this.showItemMenu = false
            this.showAttribute = false
            this.showMultiple = false
            this.showTarget = false
            this.multiples = []
            this.$refs.importUpload.clearFiles()
            if (res.code == this.$config.successCode) {
                this.items = res.data
                console.log(this.items)
            } else {
                this.$message.error(res.message)
            }
        },
        showEditName(item = null) {
            this.showEdit = true
            this.showItemMenu = false
            if (item) {
                this.currentItem = item
            }
        },
        closeEditName() {
            this.showEdit = false
            this.currentItem = { id: '' }
        },
        closeAttribute() {
            this.showAttribute = false
            this.currentItem = { id: '' }
        },
        async saveFileName() {
            let res = await this.$axios.put(this.$route('file.update', { id: this.currentItem.id }), { name: this.currentItem.name, path: this.currentItem.directory })
            this.closeEditName()
            if (res.code == this.$config.successCode) {
                this.$message.success('重命名成功')
                this.refreshData()
            } else {
                this.$message.error(res.message)
                this.refreshData()
            }
        },
        async saveAttribute() {
            let res = await this.$axios.put(this.$route('file.update', { id: this.attributeForm.id }), { name: this.attributeForm.name, visit: this.attributeForm.hide, path: this.attributeForm.directory })
            this.showAttribute = false
            if (res.code == this.$config.successCode) {
                this.refreshData()
            } else {
                this.$message.error(res.message)
            }
        },
        enterFile(item) {
            if (item.type == 'directory') {
                this.query.path = item.path
                this.$nextTick(() => {
                    this.refreshData()
                })
            }
            if (['music', 'video', 'image'].indexOf(item.type) > -1) {
                this.viewItem(item)
            }
        },
        goDirectory(path = '/') {
            this.query.path = path
            this.$nextTick(() => {
                this.refreshData()
            })
        },
        enterDirectory(item) {
            if (item.id != this.currentItem.id && !this.showEdit) {
                this.enterFile(item)
            }
        },
        openContextMenu(e) {
            e.preventDefault();
            this.showTableMenu = true;
            this.showItemMenu = false;
            this.showAttribute = false
            let minTop = document.body.clientHeight - 350
            let minLeft = document.body.clientWidth - 250
            this.tableStyle = {
                left: (e.clientX > minLeft ? minLeft : e.clientX) + 'px',
                top: (e.clientY > minTop ? minTop : e.clientY) + 'px'
            }
        },
        openContextMenuItem(item, e) {
            e.preventDefault();
            this.showItemMenu = true;
            this.currentItem = item
            this.showTableMenu = false
            this.showAttribute = false
            let minTop = document.body.clientHeight - 350
            let minLeft = document.body.clientWidth - 250
            this.itemStyle = {
                left: (e.clientX > minLeft ? minLeft : e.clientX) + 'px',
                top: (e.clientY > minTop ? minTop : e.clientY) + 'px'
            }
        }
    }
}
</script>

<style scoped lang="scss">
.viewer {
    width: 100%;
    height: 500px;
    overflow: hidden;
    background-color: var(--el-border-color-lighter);

    &.audio {
        background: var(--el-border-color-lighter) url(/assets/imgs/music.png) center center no-repeat;
    }

    audio,
    video {
        width: 100%;
        height: 100%;
    }
}

.table-list-head {
    width: 100%;
    background-color: var(--el-table-th-bg);
    text-align: center;
    line-height: 30px;
    padding: 0 !important;
    border-bottom: var(--el-border-color-light) 1px solid;
    border-left: var(--el-border-color-light) 1px solid;
    border-top: var(--el-border-color-light) 1px solid;

    :deep(.el-col) {
        border-right: var(--el-border-color-light) 1px solid;
    }
}

.table-list-item {
    width: 100%;
    font-size: 14px;
    text-align: center;
    line-height: 30px;
    padding: 0 !important;
    border-left: var(--el-border-color-light) 1px solid;
    border-bottom: var(--el-border-color-light) 1px solid;

    :deep(.el-col) {
        border-right: var(--el-border-color-light) 1px solid;
        box-sizing: border-box;
        padding: 0 10px;
        height: 30px;
    }

    .file-table-item-icon {
        position: relative;
        font-size: 24px;
        height: 30px;
    }

    .file-table-item-text {
        position: relative;
        text-align: left;
        bottom: 0;
        box-sizing: border-box;
        width: 100%;

        :deep(.el-input-group__append, .el-input-group__prepend) {
            padding: 0;
            max-width: 60px;

            button.el-button {
                width: 30px;
                margin: 0;
                padding: 0;
                border-radius: 0;

                &.el-button--danger {
                    background-color: var(--el-color-danger);
                    color: var(--el-color-white);
                }

                &.el-button--success {
                    background-color: var(--el-color-success);
                    color: var(--el-color-white);
                }
            }
        }
    }

    &.file {
        svg {
            fill: #222222;
        }
    }

    &.excel {
        svg {
            fill: #107c41;
        }
    }

    &.image {
        svg {
            fill: #fe3364;
        }
    }

    &.music {
        svg {
            fill: #0e6db0;
        }
    }

    &.pdf {
        svg {
            fill: #b40b00;
        }
    }

    &.ppt {
        svg {
            fill: #c43e1c;
        }
    }

    &.unknown {
        svg {
            fill: #2d9be5;
        }
    }

    &.word {
        svg {
            fill: #185abc;
        }
    }

    &.txt {
        svg {
            fill: #adadad;
        }
    }

    &.video {
        svg {
            fill: #874cd9;
        }
    }

    &.zip {
        svg {
            fill: var(--el-color-warning);
        }
    }

    &.other {
        svg {
            fill: var(--el-color-warning);
        }
    }

    &.folder {
        svg {
            fill: #ffe78f;
        }
    }

    &.folder-hidden {
        svg {
            fill: var(--el-border-color-lighter);
        }
    }

    &.hide {
        background-color: var(--el-table-th-bg);

        svg {
            opacity: .75;
        }
    }
}

.file {
    height: 780px;

    &-table {
        height: 680px;
        width: 100%;

        &-box {
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        &-item {
            margin-top: 20px;
            width: 100%;
            height: 0;
            padding-bottom: calc(100% + 20px);
            position: relative;
            border: var(--el-border-color-lighter) 1px dashed;
            overflow: hidden;
            border-radius: 5px;

            &:active {
                border-color: var(--el-color-primary);
            }

            &-checkbox {
                position: absolute;
                top: 0;
                right: 0;
                width: 30px;
                height: 30px;
                background-color: var(--el-border-color-lighter);
                border-bottom-left-radius: 5px;
                @extend .flexColumn;
            }

            &-icon {
                width: 100%;
                height: calc(100% - 20px);
                position: absolute;
                left: 0;
                top: 0;
                @extend .flexColumn;
                font-size: 128px;
                cursor: pointer;
            }

            :deep(.el-input-group--append > .el-input__wrapper) {
                border-radius: 0;
                border-bottom-left-radius: 5px;
            }

            :deep(.el-input-group__append, .el-input-group__prepend) {
                padding: 0;
                max-width: 60px;

                button.el-button {
                    width: 30px;
                    margin: 0;
                    padding: 0;
                    border-radius: 0;

                    &.el-button--danger {
                        background-color: var(--el-color-danger);
                        color: var(--el-color-white);
                    }

                    &.el-button--success {
                        background-color: var(--el-color-success);
                        color: var(--el-color-white);
                    }
                }
            }

            &-text {
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: -5px;
                font-size: 14px;
                cursor: text;
                box-sizing: border-box;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
            }

            &.file {
                svg {
                    fill: #222222;
                }
            }

            &.excel {
                svg {
                    fill: #107c41;
                }
            }

            &.image {
                svg {
                    fill: #fe3364;
                }
            }

            &.music {
                svg {
                    fill: #0e6db0;
                }
            }

            &.pdf {
                svg {
                    fill: #b40b00;
                }
            }

            &.ppt {
                svg {
                    fill: #c43e1c;
                }
            }

            &.unknown {
                svg {
                    fill: #2d9be5;
                }
            }

            &.word {
                svg {
                    fill: #185abc;
                }
            }

            &.txt {
                svg {
                    fill: #adadad;
                }
            }

            &.video {
                svg {
                    fill: #874cd9;
                }
            }

            &.zip {
                svg {
                    fill: var(--el-color-warning);
                }
            }

            &.other {
                svg {
                    fill: var(--el-color-warning);
                }
            }

            &.folder {
                svg {
                    fill: #ffe78f;
                }
            }

            &.folder-hidden {
                svg {
                    fill: var(--el-border-color-lighter);
                }
            }

            &.hide {
                background-color: var(--el-table-th-bg);

                svg {
                    opacity: .75;
                }
            }
        }
    }

    &-menu {
        position: fixed;
        width: 100px;
        top: 200px;
        padding: 5px;
        background: #ededef;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, .35);
        border: #bababa 1px solid;
        box-sizing: border-box;
        color: #4e4e4f;
        @extend .flexColumn;

        .disabled {
            color: #b0b0b2;
        }

        &-group {
            width: 100%;
            height: 30px;
            @extend .flexColumn;
            box-sizing: border-box;
            flex-direction: row;
        }

        &-item {
            width: 100%;
            height: 30px;
            @extend .flexColumn;
            box-sizing: border-box;
            border-radius: 2px;
            align-items: flex-start;
            padding: 0 5px;
            box-sizing: border-box;
            cursor: pointer;

            &.grid {
                width: 30px;
                align-items: center;
                font-size: 18px;
            }

            &:hover,
            &:active {
                background-color: #e0e0e4;
            }
        }

        &-divider {
            height: 1px;
            width: 100%;
            background: #c6c6c8;
            margin: 5px 0;
        }
    }

    &-attribute {
        position: fixed;
        width: 400px;
        min-height: 500px;
        max-height: 700px;
        top: 200px;
        background: #ededef;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, .35);
        border: #bababa 1px solid;
        box-sizing: border-box;
        color: #4e4e4f;
        @extend .flexColumn;
        overflow: hidden;

        &-title {
            height: 30px;
            width: 100%;
            background-color: var(--el-color-white);
            @extend .flexColumn;
            flex-direction: row;
            justify-content: space-between;
            box-sizing: border-box;
            padding: 0 10px;
        }

        &-list {
            width: 380px;
            flex: 1;
            margin: 20px 10px;
            box-sizing: border-box;
            padding: 10px;
            background-color: var(--el-color-white);

            &-item {
                height: 36px;
                width: 100%;
                display: flex;
                flex-direction: row;
                align-items: center;

                &-label {
                    width: 60px;
                    margin-right: 10px;
                }

                &-text {
                    flex: 1;
                }

                &.input {
                    height: 60px;
                }

                &-input {
                    width: 100%;
                }

                &-icon {
                    width: 60px;
                    font-size: 32px;
                    margin-right: 10px;
                    @extend .flexColumn;

                    &.file {
                        svg {
                            fill: #222222;
                        }
                    }

                    &.excel {
                        svg {
                            fill: #107c41;
                        }
                    }

                    &.image {
                        svg {
                            fill: #fe3364;
                        }
                    }

                    &.music {
                        svg {
                            fill: #0e6db0;
                        }
                    }

                    &.pdf {
                        svg {
                            fill: #b40b00;
                        }
                    }

                    &.ppt {
                        svg {
                            fill: #c43e1c;
                        }
                    }

                    &.unknown {
                        svg {
                            fill: #2d9be5;
                        }
                    }

                    &.word {
                        svg {
                            fill: #185abc;
                        }
                    }

                    &.txt {
                        svg {
                            fill: #adadad;
                        }
                    }

                    &.video {
                        svg {
                            fill: #874cd9;
                        }
                    }

                    &.zip {
                        svg {
                            fill: var(--el-color-warning);
                        }
                    }

                    &.other {
                        svg {
                            fill: var(--el-color-warning);
                        }
                    }

                    &.folder {
                        svg {
                            fill: #ffe78f;
                        }
                    }

                    &.folder-hidden {
                        svg {
                            fill: var(--el-border-color-lighter);
                        }
                    }

                    &.hide {
                        background-color: var(--el-table-th-bg);

                        svg {
                            opacity: .75;
                        }
                    }
                }
            }

            &-divider {
                height: 1px;
                width: 100%;
                background: #c6c6c8;
                margin: 10px 0;
            }
        }

        &-bottom {
            width: 100%;
            height: 50px;
            box-sizing: border-box;
            padding: 0 10px;
            @extend .flexColumn;
            justify-content: flex-end;
            flex-direction: row;
        }
    }
}
</style>