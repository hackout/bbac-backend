<template>
	<div class="user-bar">
		<div class="user-bar-item" @click="screen">
			<el-icon><el-icon-full-screen /></el-icon>
		</div>
		<div class="user-bar-item">
			<el-popover placement="bottom-end" popper-style="background:#f6f8f9" @before-enter="getMessageList"
				:width="400" trigger="click">
				<template #reference>
					<el-badge :hidden="unread == 0" :value="unread" class="badge" type="danger">
						<el-icon><el-icon-chat-dot-round /></el-icon>
					</el-badge>
				</template>
				<el-scrollbar max-height="400px">
					<el-empty v-if="messages.length == 0" description="暂无消息"></el-empty>
					<div class="user-bar-item-message" v-else>
						<div class="user-bar-item-message-item" :class="{ unread: !message.is_read }"
							@click="enterMessage(message)" style="max-width: 480px" v-for="(message, index) in messages"
							:key="index">
							<div class="user-bar-item-message-item-title">
								<span>{{ convertType(message.type) }}</span>
								<span>{{ $tool.dateFormatPlus(message.created_at) }}</span>
							</div>
							<p>{{ message.title }}</p>
						</div>
					</div>
				</el-scrollbar>
			</el-popover>
		</div>
		<div class="user-bar-item" @click="$goTo('profile.index')">
			<el-avatar style="margin-right: 6px;" :size="30" :src="userInfo.profile.avatar">{{ avatar }}</el-avatar>
			<span>{{ userInfo.profile.name }}</span>
		</div>
		<div class="user-bar-item" @click="goLogout">
			<span>退出登录</span>
		</div>
		<el-dialog v-model="messageVisit" append-to-body :title="titles[item.type]" @opened="readMessage"
			@closed="messageVisit = false">
			<div class="notice">
				<div class="notice-title">
					<h3>{{ item.title }}</h3>
				</div>
				<div class="notice-header">
					<div class="notice-header-item">
						<span class="notice-header-icon">
							<el-icon-timer></el-icon-timer>
						</span>
						<span>{{ $tool.dateFormat(item.created_at) }}</span>
					</div>
					<div class="notice-header-item">
						<span class="notice-header-icon">
							<el-icon-user></el-icon-user>
						</span>
						<span>{{ item.from }}</span>
					</div>
					<div class="notice-header-item">
						<span class="notice-header-icon">
							<el-icon-price-tag></el-icon-price-tag>
						</span>
						<span>{{ titles[item.type] }}</span>
					</div>
				</div>
				<div class="notice-content" v-if="item.extra_type != 'torque' && item.extra_type != 'examine'">
					<article v-html="extraItem.content">
					</article>
				</div>
				<div class="notice-content" v-if="item.extra_type == 'examine'">
					<div class="notice-table-name">
						<span>变更标题</span>
					</div>
					<div class="notice-table-content">
						<span>{{ extraItem.name }}</span>
					</div>
					<div class="notice-table-name">
						<span>变更说明</span>
					</div>
					<div class="notice-table-content">
						<span>{{ extraItem.content }}</span>
					</div>
					<div class="notice-table-name">
						<span>影响区域</span>
					</div>
					<div class="notice-table-content">
						<span>{{ extraItem.concerns }}</span>
					</div>
					<div class="notice-table-name">
						<span>关注事项</span>
					</div>
					<div class="notice-table-content">
						<span>{{ extraItem.influence }}</span>
					</div>
					<template
						v-if="changed.updated.length > 0 || changed.created.length > 0 || changed.deleted.length > 0">
						<div class="notice-table-name">
							<span>变更内容</span>
						</div>
						<div class="notice-table-content">
							<el-collapse accordion style="width:100%;">
								<el-collapse-item v-if="changed.created.length > 0"
									:title="`新增${changed.created.length}条考核项(查看)`" name="created">
									<el-table class="DataTable" border size="small" :data="changed.created">
										<el-table-column label="序号" align="center" prop="id" width="50">
											<template #default="scope">
												<span>{{ scope.$index + 1 }}</span>
											</template>
										</el-table-column>
										<el-table-column label="变更项" align="center" prop="code" width="120">
											<template #default="scope">
												<span>{{ changeText[scope.row.code] }}</span>
											</template>
										</el-table-column>
										<el-table-column label="影响内容" align="center" prop="content"></el-table-column>
									</el-table>
								</el-collapse-item>
								<el-collapse-item v-if="changed.deleted.length > 0"
									:title="`删除${changed.deleted.length}条考核项(查看)`" name="deleted">
									<el-table border class="DataTable" size="small" :data="changed.deleted">
										<el-table-column label="序号" align="center" prop="id" width="50">
											<template #default="scope">
												<span>{{ scope.$index + 1 }}</span>
											</template>
										</el-table-column>
										<el-table-column label="变更项" align="center" prop="code" width="120">
											<template #default="scope">
												<span>{{ changeText[scope.row.code] }}</span>
											</template>
										</el-table-column>
										<el-table-column label="影响内容" align="center" prop="content"></el-table-column>
									</el-table>
								</el-collapse-item>
								<el-collapse-item v-if="changed.updated.length > 0"
									:title="`更新${changed.updated.length}条内容(查看)`" name="updated">
									<el-table border class="DataTable" size="small" :data="changed.updated">
										<el-table-column label="序号" align="center" prop="id" width="50">
											<template #default="scope">
												<span>{{ scope.$index + 1 }}</span>
											</template>
										</el-table-column>
										<el-table-column label="变更项" align="center" prop="code" width="120">
											<template #default="scope">
												<span>{{ changeText[scope.row.code] }}</span>
											</template>
										</el-table-column>
										<el-table-column label="变更前" align="center" prop="before"
											width="120"></el-table-column>
										<el-table-column label="变更后" align="center" prop="content"></el-table-column>
									</el-table>
								</el-collapse-item>
							</el-collapse>
						</div>
					</template>
				</div>
				<div class="notice-content" v-if="item.extra_type == 'torque' && changeList.length > 0">
					<div class="notice-table-name">
						<span>变更标题</span>
					</div>
					<div class="notice-table-content">
						<span>{{ extraItem.name }}</span>
					</div>
					<div class="notice-table-name">
						<span>变更内容</span>
					</div>
					<div class="notice-table-content">
						<el-table border class="DataTable" size="small" :data="changeList">
							<el-table-column label="序号" align="center" prop="id" width="50">
								<template #default="scope">
									<span>{{ scope.$index + 1 }}</span>
								</template>
							</el-table-column>
							<el-table-column label="变更项" align="center" prop="field" width="120">
								<template #default="scope">
									<span>{{ changeFields[scope.row.field] }}</span>
								</template>
							</el-table-column>
							<el-table-column label="变更前" align="center" prop="before" width="120">
								<template #default="scope">
									<el-text tag="del" type="danger">{{ scope.row.before }}</el-text>
								</template>
							</el-table-column>
							<el-table-column label="变更后" align="center" prop="content"></el-table-column>
						</el-table>
					</div>
				</div>
				<div class="notice-footer">
					<el-button @click="messageVisit = false">关闭</el-button>
					<el-popconfirm title="确定删除此消息?" @confirm="deleteNotice">
						<template #reference>
							<el-button type="danger">
								<span>删除</span>
							</el-button>
						</template>
					</el-popconfirm>
					<el-button type="success" v-if="item.need_approve" @click="approvePass"
						icon="el-icon-check">通过</el-button>
					<el-button type="danger" v-if="item.need_approve" @click="approveReject"
						icon="el-icon-close">拒绝</el-button>
				</div>
			</div>
		</el-dialog>
	</div>
</template>

<script>
let _timer;
export default {
	data() {
		return {
			userInfo: {
				username: '',
				email: '',
				number: '',
				mobile: '',
				profile: {
					name: '',
					avatar: ''
				}
			},
			avatar: '',
			messages: [],
			unread: 0,
			options: [],
			messageVisit: false,
			item: {
				type: 'message'
			},
			titles: {
				1: '信息分享',
				2: '消息通知',
				3: '工艺变更',
				4: '任务通知',
				5: '审批任务'
			},
			extraItem: null,
			changeText: {
				name: '名称',
				description: '备注说明',
				period: '标准工时',
				engine: '考核机型',
				items: '考核项'
			},
			changed: {
				created: [],
				deleted: [],
				updated: []
			},
			changeList: [],
			changeFields: {
				plant: '工厂',
				line: '产线',
				engine: '机型',
				vehicle_type: '车型',
				assembly_id: '总成号',
				number: '螺栓编号',
				content_zh: '中文描述',
				content_en: '英文描述',
				quantity: '请输入螺栓数量',
				model: '分类1',
				type: '分类2',
				status: '开放状态',
				stage: '项目阶段',
				station: '工位',
				sub_station: '工位2',
				special: '特殊特性',
				param: '参数',
				torque_target: '目标扭矩',
				torque_lower: '扭矩下限',
				torque_upper: '扭矩上限',
				angle_target: '角度标准',
				angle_lower: '角度下限',
				angle_upper: '角度上限',
				lasted_at: '最近放行时间',
				expected_at: '预计放行时间',
				final_at: '最终放行时间',
				start_torque: '起始扭矩',
				residual_torque: '转矩角',
				pfu_test: 'PFU测试值',
				pfu_lower: 'PFU考核下限',
				pfu_upper: 'PFU考核上限',
				pfu_early_lower: 'PFU预警上限',
				pfu_early_upper: 'PFU预警下限',
				l_pfu_test: 'L-PFU测试值',
				l_pfu_lower: 'L-PFU考核下限',
				l_pfu_upper: 'L-PFU考核上限',
				l_pfu_early_lower: 'L-PFU预警上限',
				l_pfu_early_upper: 'L-PFU预警下限'
			}
		}
	},
	created() {
		this.$nextTick(() => {
			this.userInfo = this.$page.props.user
			this.avatar = this.userInfo.profile.pinyin.substring(0, 1).toUpperCase()
			this.getMessage()
			this.getMessageType()
		})
	},
	unmounted() {
		_timer && clearTimeout(_timer)
	},
	methods: {
		async deleteNotice() {
			var res = await this.$axios.delete(this.$route('profile.delete', { id: this.item.id }))
			if (res.code == this.$config.successCode) {
				this.$message.success('删除消息成功')
				this.messageVisit = false
			} else {
				this.$message.error(res.message)
			}
		},
		async approvePass() {
			var res = await this.$axios.post(this.$route('profile.approve', { id: this.item.id }), { pass: 1 })
			if (res.code == this.$config.successCode) {
				this.$message.success('审批成功')
				this.messageVisit = false
			} else {
				this.$message.error(res.message)
			}
		},
		approveReject() {
			this.$prompt('请输入操作理由', '拒绝通过').then(async ({ value }) => {
				var res = await this.$axios.post(this.$route('profile.approve', { id: this.item.id }), { pass: 0, description: value })
				if (res.code == this.$config.successCode) {
					this.$message.success('处理消息成功')
					this.messageVisit = false
				} else {
					this.$message.error(res.message)
				}
			}).catch(() => { })
		},
		convertType(val) {
			const res = this.options.filter(n => n.value == val);
			if (res.length == 0) return '-'
			return res[0].name
		},
		async getMessageType() {
			const res = await this.$axios.get(this.$route('dict.option', { code: 'notice_type' }))
			if (res.code == this.$config.successCode) {
				this.options = res.data
			}
		},
		async getMessage() {
			const res = await this.$axios.get(this.$route('profile.unread'))
			if (res.code == this.$config.successCode) {
				this.unread = parseInt(res.data)
			}
			/*_timer = setTimeout(() => {
				this.getMessage()
			}, 60 * 1000);*/
		},
		async getMessageList() {
			const res = await this.$axios.get(this.$route('profile.message'), { limit: 50, page: 1 })
			if (res.code == this.$config.successCode) {
				this.messages = res.data.items
			}
		},
		goProfile() {
			window.location.href = this.$route('profile')
		},
		goLogout() {
			this.$confirm('确定退出当前登录?', '操作提示', {
				type: 'warning',
				confirmButtonText: '退出登录',
				confirmButtonClass: 'el-button--danger'
			}).then(() => {
				this.$ajax.post(this.$route('logout'))
			}).catch(() => {
			})
		},
		screen() {
			this.$tool.screen(document.documentElement)
		},
		enterMessage(message) {
			this.item = message
			if (message.extra_type == 'examine') {
				this.changed.created = message.extra.created
				this.changed.deleted = message.extra.deleted
				this.changed.updated = message.extra.updated
			}

			this.getMessageData()
		},
		async readMessage() {
			var res = await this.$axios.post(this.$route('profile.read', { id: this.item.id }))
			if (res.code != this.$config.successCode) {
				this.$message.error(res.message)
				this.messageVisit = false;
			}
		},
		async getMessageData() {
			var res = await this.$axios.get(this.$route('profile.message_detail', { id: this.item.id }))
			if (res.code == this.$config.successCode) {
				this.extraItem = res.data
				if (res.data.type == 'torque') {
					this.changeList = res.data.extra
				}
				this.messageVisit = true
			} else {
				this.$message.error(res.message)
				this.messageVisit = false;
			}
			this.readMessage()
		}
	}
}
</script>

<style scoped lang="scss">
.user-bar {
	display: flex;
	align-items: center;
	height: 100%;

	&-item {
		margin-right: 15px;
		display: flex;
		flex-direction: row;
		align-items: center;
		cursor: pointer;
		justify-content: center;

		span {
			font-size: 18px;
		}

		:deep(.el-icon) {
			width: 24px;
			height: 24px;
			font-size: 24px;
			color: var(--el-text-color);
		}

		&:last-child {
			border-left: var(--el-border-light) var(--el-border-width) var(--el-border-style);
			padding-left: 15px;
		}

		&:nth-child(3) {
			margin-left: 15px;
		}

		&-message {
			width: 370px;
			display: flex;
			flex-direction: column;
			box-sizing: border-box;

			&-item {
				box-sizing: border-box;
				width: 100%;
				padding: 15px;
				display: flex;
				flex-direction: column;
				border-bottom: var(--el-border-light) var(--el-border-width) var(--el-border-style);
				font-size: 12px;
				cursor: pointer;

				&.unread {
					background-color: var(--el-color-danger-light-9);
				}

				&-title {
					width: 100%;
					@extend .flexColumn;
					flex-direction: row;
					justify-content: space-between;
					margin-bottom: 5px;

					span {
						font-size: 14px;
						font-weight: 400;

						&:last-child {
							font-size: 12px;
							color: var(--el-border-light)
						}
					}
				}

			}
		}
	}
}

.notice {
	width: 100%;
	height: auto;
	@extend .flexColumn;
	padding: 0 16px;

	&-title {
		width: 100%;
		min-height: 60px;
		line-height: 34px;
		font-size: 16px;
		font-weight: bold;
		text-align: left;
	}

	&-header {
		height: 40px;
		width: 100%;
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: flex-start;
		font-size: 14px;
		border-bottom: var(--el-border-light) 1px solid;
		margin-bottom: 15px;

		&-item {
			height: 40px;
			margin: 0 6px;
			@extend .flexColumn;
			flex-direction: row;
		}
	}

	&-content {
		width: 100%;
		margin-bottom: 20px
	}

	&-approve {
		background-color: var(--el-text-color-placeholder);
		margin: 10px 0;
		padding: 5px 10px;
		border-radius: 5px;
	}

	&-table-name {
		font-size: 16px;
		color: var(--el-color-primary);
	}

	&-table-content {
		padding: 10px 15px;
		width: 100%;
		box-sizing: border-box;

		&-title {
			margin-top: 20px;
			height: 30px;
			font-size: 14px;
		}
	}
}

.DataTable.el-table {
	--el-table-border-color: var(--el-border-light);
}

.DataTable :deep(th.el-table__cell) {
	background-color: var(--el-table-th-bg);
}

.DataTable :deep(thead) {
	color: var(--el-table-th);
}

.DataTable :deep(thead th) {
	font-weight: 200;
}

.DataTable :deep(.el-table__footer) .cell {
	font-weight: bold;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-horizontal {
	height: 12px;
	border-radius: 12px;
}

.DataTable :deep(.el-table__body-wrapper) .el-scrollbar__bar.is-vertical {
	width: 12px;
	border-radius: 12px;
}

.col-text {
	width: 100%;
	height: 200px;
	@extend .flexColumn;
	font-size: 18px;
	border: var(--el-border-light) 1px solid;
	border-radius: 10px;
	overflow: hidden;
	cursor: pointer;
}
</style>
