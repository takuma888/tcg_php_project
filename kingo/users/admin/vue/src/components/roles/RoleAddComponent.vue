<template>
  <span>
    <el-button :type="type" :size="size" @click="showDialog">
      <i class="fa fa-plus"></i>&nbsp;&nbsp;添加
    </el-button>
    <!-- dialogs -->
    <el-dialog title="添加角色" append-to-body :visible.sync="dialogVisible">
      <el-form :model="form" ref="form" status-icon :rules="rules" >
        <el-form-item label="ID" prop="id" size="small">
          <el-input type="text" v-model="form.id"></el-input>
        </el-form-item>
        <el-form-item label="名称" prop="name" size="small">
          <el-input type="text" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="优先级" size="small">
          <el-input type="text" v-model="form.priority"></el-input>
        </el-form-item>
        <el-form-item label="描述" size="small" :autosize="{ minRows: 2 }">
          <el-input type="textarea" v-model="form.desc"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="submitDialogForm">确 定</el-button>
      </span>
    </el-dialog>
  </span>
</template>

<script>
import Api from '@/api'
export default {
  props: ['size', 'type'],
  data () {
    return {
      dialogVisible: false,
      form: {
        id: '',
        name: '',
        priority: 0,
        desc: ''
      },
      rules: {
        id: [
          { required: true, message: '请输入ID', trigger: 'blur' },
          {
            validator: (rule, value, callback) => {
              if (value) {
                Api.role.validateIdUnique(value).then((data) => {
                  if (data && data.invalid) {
                    callback(new Error(data.invalid))
                  } else {
                    callback()
                  }
                })
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        name: [
          { required: true, message: '请输入名称', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    showDialog () {
      this.dialogVisible = true
      if (this.$refs.form) {
        this.$refs.form.resetFields()
        this.form.desc = ''
      }
    },
    submitDialogForm () {
      this.$refs.form.validate((valid) => {
        if (valid) {
          Api.role.add(this.form.id, this.form.name, this.form.priority, this.form.desc).then(() => {
            this.dialogVisible = false
            this.$refs.form.resetFields()
            this.form.desc = ''
            this.$emit('parent')
          })
        } else {
          this.$message({
            message: '验证出错',
            type: 'error'
          })
          return false
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped></style>
