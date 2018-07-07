#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys
import os
from dependencies import yaml

sys.dont_write_bytecode = True  # 不写二进制文件


# 获取配置
def get_config(key, files):
    c = {}
    for i in files:
        if os.path.exists(i):
            d = yaml.load(open(i))
            c = dict(c.items() + d.items())

    for k in key.split('.'):
        if k in c:
            c = c[k]
        else:
            return None
    return c


if __name__ == '__main__':
    if len(sys.argv) < 3:
        sys.exit(1)
    key = sys.argv[1]
    config_files = []

    for i in range(2, len(sys.argv)):
        config_files.append(sys.argv[i])

    config = get_config(key, config_files)
    if config is not None:
        try:
            print config
        except:
            print config.encode('utf-8')
    else:
        sys.exit(0)
