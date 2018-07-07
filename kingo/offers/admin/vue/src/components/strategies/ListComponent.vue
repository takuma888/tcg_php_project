<template>
  <mu-container fluid>
    <mu-row style="margin-bottom: 16px;">
      <mu-col :span="12">
        <mu-flex align-items="center" justify-content="between">
          <mu-button color="primary" @click.stop="openCreateStrategyModal"><i class="fa fa-plus"></i>&nbsp;添加策略</mu-button>
          <mu-pagination raised circle :total="total" :current.sync="page" :page-size="size" @change="pager"></mu-pagination>
        </mu-flex>
      </mu-col>
    </mu-row>
    <mu-row>
      <mu-col :span="12">
        <mu-expansion-panel v-for="(value, key1) in data" :key="key1" :expand="panel === value.base.id" @change="togglePanel(value.base.id)">
          <div slot="header" style="width: 100%">
            <mu-flex justify-content="between" align-items="center">
              <div style="width: 10%;">
                <template v-if="value.base.priority > 0">
                  <strong style="color: red;">{{ value.base.name }}</strong>&nbsp;
                </template>
                <template v-else>
                  <strong style="color: gray;">{{ value.base.name }}</strong>&nbsp;
                </template>
              </div>
              <div>
                <small><i>国家: <strong>{{ value.base.geo2 ? value.base.geo2 : '全球' }}/{{ value.base.geo3 ? value.base.geo3 : '全球' }}</strong></i></small>
              </div>
              <div>
                <small><i>起始: <strong>{{ value.base.start_at ? value.base.start_at : '无' }}</strong></i></small>&nbsp;
                <small><i>结束: <strong>{{ value.base.end_at ? value.base.end_at : '无' }}</strong></i></small>
              </div>
              <div>
                <small><i>客户端: <strong>{{ value.base.client ? value.base.client : '不限' }}</strong></i></small>
              </div>
              <div>
                <small><i>优先级: <strong>{{ value.base.priority }}</strong></i></small>
              </div>
              <div>
                <small><i>创建: <strong>{{ value.base.create_at }}</strong>&nbsp;&nbsp;更新: <strong>{{ value.base.update_at }}</strong></i></small>
                <mu-button small flat color="primary" @click.stop="openEditStrategyModal(value.base.id)"><i class="fa fa-edit"></i>&nbsp;编辑</mu-button>
                <mu-button small flat color="secondary" @click.stop="deleteStrategy(value.base.id)"><i class="fa fa-remove"></i>&nbsp;删除</mu-button>
              </div>
            </mu-flex>
          </div>
          <p>{{ value.base.description }}</p>
          <el-table size="small" :show-header="false" border :span-method="tableSpanMethod" :data="value.ext" style="width: 100%">
            <el-table-column prop="category" label="分类" width="50" >
              <strong slot-scope="scope">
                {{ scope.row.category === 'country' ? '国家' : '' }}
                {{ scope.row.category === 'source' ? '来源' : '' }}
                {{ scope.row.category === 'package_name' ? '包名' : '' }}
                {{ scope.row.category === 'id' ? 'ID' : '' }}
                {{ scope.row.category === 'package_size' ? '包大小' : '' }}
              </strong>
            </el-table-column>
            <el-table-column prop="type" label="类型" width="50">
              <i slot-scope="scope">
                {{ scope.row.type === 'include' ? '包含' : '' }}
                {{ scope.row.type === 'exclude' ? '排除' : '' }}
              </i>
            </el-table-column>
            <el-table-column prop="values" label="值">
              <template slot-scope="scope">
                <template v-if="scope.row.category === 'package_size'">
                  <el-tag
                    size="small"
                    :key="key2"
                    v-for="(item, key2) in scope.row.values"
                    closable
                    :disable-transitions="false"
                    @close="removeExtTag(item.id)"
                  >
                    {{item.value1}} 至 {{item.value2}}
                  </el-tag>
                </template>
                <template v-else-if="scope.row.category === 'country'">
                  <el-tag
                    size="small"
                    :key="key2"
                    v-for="(item, key2) in scope.row.values"
                    closable
                    :disable-transitions="false"
                    @close="removeExtTag(item.id)"
                  >
                    {{item.value1}} / {{item.value2}}
                  </el-tag>
                </template>
                <template v-else>
                  <el-tag
                    size="small"
                    :key="key2"
                    v-for="(item, key2) in scope.row.values"
                    closable
                    :disable-transitions="false"
                    @close="removeExtTag(item.id)"
                  >
                    {{item.value1}}
                  </el-tag>
                </template>
                <mu-button color="primary" small flat @click="openCreateStrategyExtModal(value.base.id, scope.row.category, scope.row.type)">
                  <i class="fa fa-plus"></i>&nbsp;...&nbsp;添加
                </mu-button>
              </template>
            </el-table-column>
          </el-table>
        </mu-expansion-panel>
      </mu-col>
    </mu-row>
    <el-dialog :visible.sync="showCreateStrategyModal" append-to-body top="50px">
      <div slot="title">
        <span class="el-dialog__title"><i class="fa fa-star-o"></i>&nbsp;创建策略</span>
      </div>
      <el-form :model="createForm" ref="createForm" status-icon :rules="rules" >
        <el-form-item label="名称" prop="name" size="small">
          <el-input type="text" v-model="createForm.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="描述" prop="description" size="small">
          <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="createForm.description" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="优先级" prop="priority" size="small">
          <el-input type="text" v-model="createForm.priority" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="客户端" prop="client" size="small">
          <el-input type="text" v-model="createForm.client" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="国家 ISO3166-alpha-2" prop="geo2" size="small">
          <el-input type="text" v-model="createForm.geo2" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="国家 ISO3166-alpha-3" prop="geo3" size="small">
          <el-input type="text" v-model="createForm.geo3" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="起始时间" prop="start_at" size="small">
          <el-date-picker
            v-model="createForm.start_at" style="width: 100%;" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss"
            type="datetime"
            placeholder="选择日期时间">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间" prop="end_at" size="small">
          <el-date-picker
            v-model="createForm.end_at" style="width: 100%;" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss"
            type="datetime"
            placeholder="选择日期时间">
          </el-date-picker>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <mu-button @click="showCreateStrategyModal = false">取 消</mu-button>
        <mu-button color="primary" @click="createStrategy">提交</mu-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="showEditStrategyModal" append-to-body top="50px">
      <div slot="title">
        <span class="el-dialog__title"><i class="fa fa-edit"></i>&nbsp;编辑策略</span>
      </div>
      <el-form :model="editForm" ref="editForm" status-icon :rules="rules" >
        <el-form-item label="名称" prop="name" size="small">
          <el-input type="text" v-model="editForm.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="描述" prop="description" size="small">
          <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="editForm.description" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="优先级" prop="priority" size="small">
          <el-input type="text" v-model="editForm.priority" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="客户端" prop="client" size="small">
          <el-input type="text" v-model="editForm.client" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="国家 ISO3166-alpha-2" prop="geo2" size="small">
          <el-input type="text" v-model="editForm.geo2" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="国家 ISO3166-alpha-3" prop="geo3" size="small">
          <el-input type="text" v-model="editForm.geo3" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="起始时间" prop="start_at" size="small">
          <el-date-picker
            v-model="editForm.start_at" style="width: 100%;" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss"
            type="datetime"
            placeholder="选择日期时间">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间" prop="end_at" size="small">
          <el-date-picker
            v-model="editForm.end_at" style="width: 100%;" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss"
            type="datetime"
            placeholder="选择日期时间">
          </el-date-picker>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <mu-button @click="showEditStrategyModal = false">取 消</mu-button>
        <mu-button color="primary" @click="editStrategy">提交</mu-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="showCreateStrategyExtModal" append-to-body width="70%">
      <div slot="title">
        <span class="el-dialog__title"><i class="fa fa-cogs"></i>&nbsp;创建策略配置</span>
      </div>
      <el-form inline :model="createExtForm" ref="createExtForm" status-icon :rules="extRules" >
        <el-form-item size="small">
          <el-select v-model="createExtForm.category" disabled placeholder="请选择">
            <el-option
              v-for="item in [
                { label: '国家', value: 'country' },
                { label: '来源', value: 'source' },
                { label: '包名', value: 'package_name' },
                { label: 'ID', value: 'id' },
                { label: '包大小', value: 'package_size' }
              ]"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item size="small">
          <el-select v-model="createExtForm.type" disabled placeholder="请选择">
            <el-option
              v-for="item in [
                { label: '包含', value: 'include' },
                { label: '排除', value: 'exclude' }
              ]"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item prop="value1" label="值1" size="small">
          <el-input v-model="createExtForm.value1"></el-input>
        </el-form-item>
        <el-form-item v-if="createExtForm.category === 'package_size' || createExtForm.category === 'country'" prop="value2" label="值2" size="small">
          <el-input v-model="createExtForm.value2"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <mu-button @click="showCreateStrategyExtModal = false">取 消</mu-button>
        <mu-button color="primary" @click="createStrategyExt">提交</mu-button>
      </div>
    </el-dialog>
  </mu-container>
</template>

<script>
export default {
  data () {
    return {
      data: [],
      page: 1,
      size: 25,
      total: 0,
      panel: 0,
      showCreateStrategyModal: false,
      showEditStrategyModal: false,
      showCreateStrategyExtModal: false,
      createForm: {
        name: '',
        description: '',
        priority: 0,
        client: '',
        geo2: '',
        geo3: '',
        start_at: '',
        end_at: ''
      },
      editForm: {
        id: 0,
        name: '',
        description: '',
        priority: 0,
        client: '',
        geo2: '',
        geo3: '',
        start_at: '',
        end_at: ''
      },
      createExtForm: {
        strategyId: 0,
        category: '',
        type: '',
        value1: '',
        value2: ''
      },
      rules: {
        name: [
          { required: true, message: '请输入策略名称', trigger: 'blur' }
        ]
      },
      extRules: {
        value1: [
          { required: true, message: '请输入值1', trigger: 'blur' }
        ],
        value2: [
          {
            validator: (rule, value, callback) => {
              if (this.createExtForm.category === 'package_size' && value === '') {
                callback(new Error('包大小必须输入值2'))
              } else if (this.createExtForm.category === 'country' && value === '') {
                callback(new Error('国家必须输入值2'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      }
    }
  },
  methods: {
    getData () {
      this.$api.strategies.list({
        page: this.page,
        size: this.size
      }).then((data) => {
        this.data = data.data
        this.total = data.total
      })
    },
    togglePanel (panel) {
      this.panel = panel === this.panel ? 0 : panel
    },
    pager (value) {
      this.page = value
      this.getData()
    },
    openCreateStrategyExtModal (strategyId, category, type) {
      this.createExtForm.strategyId = strategyId
      this.createExtForm.category = category
      this.createExtForm.type = type
      this.createExtForm.value1 = ''
      this.createExtForm.value2 = ''
      this.showCreateStrategyExtModal = true
      if (this.$refs.createExtForm) {
        this.$refs.createExtForm.resetFields()
      }
    },
    openEditStrategyModal (id) {
      this.editForm = {
        id: 0,
        name: '',
        description: '',
        priority: 0,
        client: '',
        geo2: '',
        geo3: '',
        start_at: '',
        end_at: ''
      }
      if (this.$refs.editForm) {
        this.$refs.editForm.resetFields()
      }
      this.$api.strategy.get(id).then((data) => {
        this.editForm = {
          id: data.base.id,
          name: data.base.name,
          description: data.base.description,
          priority: data.base.priority,
          client: data.base.client,
          geo2: data.base.geo2,
          geo3: data.base.geo3,
          start_at: data.base.start_at,
          end_at: data.base.end_at
        }
        this.showEditStrategyModal = true
      }).catch(() => {})
    },
    editStrategy () {
      this.$refs.editForm.validate((valid) => {
        if (valid) {
          this.$api.strategy.update(
            this.editForm.id,
            this.editForm.name,
            this.editForm.description,
            this.editForm.priority,
            this.editForm.client,
            this.editForm.geo2,
            this.editForm.geo3,
            this.editForm.start_at,
            this.editForm.end_at
          ).then(() => {
            this.getData()
            this.showEditStrategyModal = false
          }).catch(() => {})
        }
      })
    },
    deleteStrategy (id) {
      this.$confirm('确认删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$api.strategy.delete(id).then(() => {
          this.getData()
        })
      }).catch(() => {})
    },
    openCreateStrategyModal () {
      this.createForm = {
        name: '',
        description: '',
        priority: 0,
        client: '',
        geo2: '',
        geo3: '',
        start_at: '',
        end_at: ''
      }
      if (this.$refs.createForm) {
        this.$refs.createForm.resetFields()
      }
      this.showCreateStrategyModal = true
    },
    createStrategy () {
      this.$refs.createForm.validate((valid) => {
        if (valid) {
          this.$api.strategy.add(
            this.createForm.name,
            this.createForm.description,
            this.createForm.priority,
            this.createForm.client,
            this.createForm.geo2,
            this.createForm.geo3,
            this.createForm.start_at,
            this.createForm.end_at
          ).then(() => {
            this.getData()
            this.showCreateStrategyModal = false
          }).catch(() => {})
        }
      })
    },
    tableSpanMethod ({ row, column, rowIndex, columnIndex }) {
      if (columnIndex === 0) {
        if (rowIndex % 2 === 0) {
          return {
            rowspan: 2,
            colspan: 1
          }
        } else {
          return {
            rowspan: 0,
            colspan: 0
          }
        }
      }
    },
    removeExtTag (id) {
      this.$confirm('确认删除吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$api.strategy.deleteExt(id).then(() => {
          this.getData()
        })
      }).catch(() => {})
    },
    createStrategyExt () {
      this.$refs.createExtForm.validate((valid) => {
        if (valid) {
          this.$api.strategy.addExt(
            this.createExtForm.strategyId,
            this.createExtForm.category,
            this.createExtForm.type,
            this.createExtForm.value1,
            this.createExtForm.value2
          ).then(() => {
            this.showCreateStrategyExtModal = false
            this.getData()
          }).catch(() => {})
        }
      })
    }
  },
  mounted () {
    this.getData()
  }
}
</script>

<style scoped>
  .el-tag + .el-tag {
    margin-left: 10px;
  }
</style>
