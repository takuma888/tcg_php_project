#!/usr/bin/env bash
export SCRIPTDIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

source ${SCRIPTDIR}/common.sh

LOGINFO "Begin Time: $(date '+%Y-%m-%d %H:%M:%S')"

export DEBUG_ON="set -x"


function get_config {
    python ${SCRIPTDIR}/get_config.py "$1" ${SCRIPTDIR}/deploy.yml ${SCRIPTDIR}/deploy.yml
}

tmp=${SCRIPTDIR}/tmp
tmp_code=${tmp}/code

export action
export deploy_id


echo $*
echo $(getopts "a:d:" arg)
while getopts "a:d:" arg
do case ${arg} in
    a)
        echo "action is ${OPTARG}"
        action=${OPTARG}
        ;;
    d)
        echo "deploy id is ${OPTARG}"
        deploy_id=${OPTARG}
        ;;
    ?)
        ;;
   esac
done

if test "$action" == ""; then
    action="help"
else
    if test "$deploy_id" == ""; then
        echo "-d is required!"
        exit 1
    fi
fi

# 同步整个代码
function sync_all {
    if [ -d "${tmp_code}" ]; then
        rm -rf ${tmp_code}
    fi
    mkdir -p ${tmp_code}
    echo ${tmp_code}
    to=$(get_config "deploy.${deploy_id}.root_path")/
    mkdir -p ${tmp_code}/kingo
    # framework
    prepare_framework
    # tcg
    prepare_tcg
    # app users
    prepare_app_users
    # app offers
    prepare_app_offers
    # root files
    cp -a ~/Documents/self/tcg_php_project/kingo/*.php ${tmp_code}/kingo
    # config
    prepare_config

    push_sync ${deploy_id} ${tmp_code}/ ${to}
}

# 同步配置
function prepare_config {
    echo "prepare config"
    cp -a ${SCRIPTDIR}/wall/${deploy_id}/ ${tmp_code}/kingo
}

# 同步 offers
function prepare_app_offers {
    echo "prepare app offers"
    tmp_dir=${tmp_code}/kingo/offers
    if [ -d "${tmp_dir}" ]; then
        rm -rf ${tmp_dir}
    fi
    mkdir -p ${tmp_dir}
    mkdir -p ${tmp_dir}/api
    mkdir -p ${tmp_dir}/cmd
    mkdir -p ${tmp_dir}/src
    # admin
    mkdir -p ${tmp_dir}/admin
    mkdir -p ${tmp_dir}/admin/public

    cp -a ~/Documents/self/tcg_php_project/kingo/offers/*.php ${tmp_dir}
    cp -a ~/Documents/self/tcg_php_project/kingo/offers/src/ ${tmp_dir}/src
    cp -a ~/Documents/self/tcg_php_project/kingo/offers/cmd/ ${tmp_dir}/cmd
    cp -a ~/Documents/self/tcg_php_project/kingo/offers/api/ ${tmp_dir}/api

    cp -a ~/Documents/self/tcg_php_project/kingo/offers/admin/controller ${tmp_dir}/admin
    cp -a ~/Documents/self/tcg_php_project/kingo/offers/admin/template ${tmp_dir}/admin
    cp -a ~/Documents/self/tcg_php_project/kingo/offers/admin/vue/dist/ ${tmp_dir}/admin/public
}

# 同步 users
function prepare_app_users {
    echo "prepare app users"
    tmp_dir=${tmp_code}/kingo/users
    if [ -d "${tmp_dir}" ]; then
        rm -rf ${tmp_dir}
    fi
    mkdir -p ${tmp_dir}
    mkdir -p ${tmp_dir}/api
    mkdir -p ${tmp_dir}/cmd
    mkdir -p ${tmp_dir}/src
    # admin
    mkdir -p ${tmp_dir}/admin
    mkdir -p ${tmp_dir}/admin/public

    cp -a ~/Documents/self/tcg_php_project/kingo/users/*.php ${tmp_dir}
    cp -a ~/Documents/self/tcg_php_project/kingo/users/src/ ${tmp_dir}/src
    cp -a ~/Documents/self/tcg_php_project/kingo/users/cmd/ ${tmp_dir}/cmd
    cp -a ~/Documents/self/tcg_php_project/kingo/users/api/ ${tmp_dir}/api

    cp -a ~/Documents/self/tcg_php_project/kingo/users/admin/controller ${tmp_dir}/admin
    cp -a ~/Documents/self/tcg_php_project/kingo/users/admin/template ${tmp_dir}/admin
    cp -a ~/Documents/self/tcg_php_project/kingo/users/admin/vue/dist/ ${tmp_dir}/admin/public

}

# 同步 tcg
function prepare_tcg {
    echo "prepare tcg"
    tmp_dir=${tmp_code}/tcg
    if [ -d "${tmp_dir}" ]; then
        rm -rf ${tmp_dir}
    fi
    mkdir -p ${tmp_dir}
    cp -a ~/Documents/self/tcg_php_project/tcg/ ${tmp_dir}
}

# 同步框架
function prepare_framework {
    echo "prepare framework"
    tmp_dir=${tmp_code}/vendor
    if [ -d "${tmp_dir}" ]; then
        rm -rf ${tmp_dir}
    fi
    mkdir -p ${tmp_dir}
    cp -a ~/Documents/self/tcg_php_project/vendor/ ${tmp_dir}
}

# 推送同步
function push_sync {
    from=$2
    to=$3
    ssh_host=$(get_config "deploy.${deploy_id}.server")
    ssh_user=$(get_config "deploy.${deploy_id}.username")
    ssh_port=$(get_config "deploy.${deploy_id}.ssh_port")
    ssh_str=${ssh_user}@${ssh_host}
    LOGINFO "同步[$1]"
    ssh_port=${ssh_port} do_rsync ${from} ${ssh_str}:${to}
}

function do_rsync {
    local n=0
    local max=5
    local status=-1
    if [ ! -n "$ssh_port" ]; then
        local ssh_port=22
    fi

    set +e
    until [ ${status} == 0 ]; do
        if [ -n "$RSYNC_PASSWORD" ]; then
            # rsync -lrvz --size-only "$@"
            RSYNC_PASSWORD=${RSYNC_PASSWORD} rsync -lrz "$@"
        else
            rsync -lrz -e "ssh -o StrictHostKeyChecking=no -p $ssh_port" "$@"
        fi
        status=$?
        n=$((n+1))
        if [ ${n} -gt ${max} ]; then
            echo "ERROR on re-trys rsync, Exit code: $status"
            exit ${status}
        fi
        if [ ${status} != 0 ]; then
            echo "Warning: failed on re-trys... retry $n"
            sleep 1
        fi
    done
    set -e
}

function help {
    cat <<USAGE
Usage:

    ./tool.sh -a <action:sync_all> -d <deploy_id>

USAGE
    exit 0
}


run_action ${action}
exit 0