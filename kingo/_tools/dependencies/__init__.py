#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
# 可以把所有依赖的模块放到这个目录，对于独立没有子模块的模块可
# 以先用zip压缩后改名放到这个目录
#

import os
import sys

sys.dont_write_bytecode = True  # 不写二进制文件

dir_path = os.path.dirname(os.path.abspath(__file__))


def import_module(name):
    mod_path = os.path.join(dir_path, name)
    if os.path.isfile(mod_path):
        sys.path.insert(0, mod_path)
        m = __import__(name)
    elif os.path.isdir(mod_path):
        m = __import__(name)
    else:
        sys.stderr.write('can not find module: %s' % name)
        sys.exit(1)
    return m

yaml = import_module('yaml')
