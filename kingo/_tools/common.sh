#!/usr/bin/env bash

# 加上 -e参数 一旦某个语句执行出错就终止执行

SCRIPTDIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
LANG=en_US.UTF-8
DEBUG_ON=

export PS4='+${BASH_SOURCE}:${LINENO}:${FUNCNAME[0]}: '

##################################################
# 最全的日志
#LOGFILE_DEBUG=
# 正常的提示信息
#LOGFILE_INFO=
# 警告信息，应该引起关注，但是可以继续发布
#LOGFILE_WARNING=
# 错误信息，会导致发布进程中止
#LOGFILE_ERROR=

shopt -s expand_aliases
alias LOGDEBUG="set +x && log DEBUG"
alias LOGINFO="set +x && log INFO"
alias LOGWARNING="set +x && log WARNING"
alias LOGERROR="set +x && log ERROR"
##################################################



##############
## 基础方法 ##
##############
function log {
    set +x
    if [ "$#" -lt 2 ]; then
        echo "$0 <level> <msg>"
        exit
    fi
    local level=$1
    shift 1
    local log_filename=LOGFILE_${level}
    if [[ ! "${!log_filename}" ]]
    then
        echo "[$(date +'%Y-%m-%d %H:%M:%S')][${level}] $@"
    else
        log_filename=${!logfilename}
        echo "[$(date +'%Y-%m-%d %H:%M:%S')][${level}] $@" | tee -a $log_filename
    fi
    ${DEBUG_ON}
}

function check_param {
    set +x
    for i in $@
    do
        if [[ ! "${!i}" ]]
        then
            log ERROR "no input: $i"
            ${DEBUG_ON}
            exit 1
        fi
    done
    ${DEBUG_ON}
    return 0
}

function check_file {
    set +x
    for i in $@ ;do
        if ! test -f "${!i}"; then
            log ERROR "file not exists: $i"
            ${DEBUG_ON}
            exit 1
        fi
    done
    ${DEBUG_ON}
    return 0
}

function check_dir {
    set +x
    for i in $@ ;do
        if ! test -d "${!i}"; then
            log ERROR "dir not exists: $i"
            ${DEBUG_ON}
            exit 1
        fi
    done
    ${DEBUG_ON}
    return 0
}

function run_action {
    set +x
    local declared_action_name
    local action
    action=$1
    declared_action_name=$(declare -F $action || true)

    if test ! -n "$declared_action_name" ; then
       echo "$action() not found in $0"
       exit 1
    fi

    shift 1
    ${DEBUG_ON}
    $action $@
}