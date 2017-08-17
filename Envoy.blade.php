#################################
# Don't edit this file !        #
# Use Envoy.config.php instead  #
#################################

@include('./Envoy.config.php')
@servers(['web' => [$sshHost]])

@setup
    $current = $workdir . '/current';
    $dirRepo = $workdir . '/git';
    $dirShared = $workdir . '/shared';
    $dirReleases = $workdir . '/releases';
    $dirRelease = $dirReleases .'/'. date('YmdHis');
    $dirLastRelease = $dirReleases . '/last';
@endsetup

@macro('deploy')
    new_release
    composer
    npm
    cache
    assets
    database
    stop
    link_current
    start
    clear
@endmacro

#######################
# Prepare the workdir #
#######################
@task('prepare')
    echo "Task : Prepare {{ $workdir }}";
    mkdir -p {{ $dirShared }}/logs;
    mkdir -p {{ $dirShared }}/img/avatars;
    mkdir -p {{ $dirShared }}/img/boats;

    echo "Prepare git repository";
    [ -d {{ $dirRepo }} ] && rm -rf {{ $dirRepo }};
    mkdir -p {{ $dirRepo }};
    cd {{ $dirRepo }};

    echo "Clone {{ $repository }} into {{ $dirRepo }}";
    git clone -q {{ $repository }} .
@endtask


########################
# Create a new release #
########################
@task('new_release')
    echo "Task : Create new release";
    mkdir -p {{ $dirRelease }};

    echo "Update git (branch: {{ $branch }})";
    cd {{ $dirRepo }};
    git checkout -q {{ $branch }};
    git reset -q --hard;
    git pull;
    echo "Last commit : $(git log --oneline -n1)";

    echo "Copy files to new release";
    git archive {{$branch}}|tar -x -C {{ $dirRelease }};

    echo "Create links :";
    [ -e {{ $dirShared }}/parameters.yml ] || cp {{ $dirRelease }}/app/config/parameters.yml.dist {{ $dirShared }}/parameters.yml;
    ln -vs {{ $dirShared }}/parameters.yml {{ $dirRelease }}/app/config/parameters.yml;
    rm -rf {{ $dirRelease }}/var/logs;
    ln -vs {{ $dirShared }}/logs {{ $dirRelease }}/var/logs;
    rm -rf {{ $dirRelease }}/var/avatars {{ $dirRelease }}/var/boats;
    ln -vs {{ $dirShared }}/img/avatars {{ $dirRelease }}/var/avatars;
    ln -vs {{ $dirShared }}/img/boats {{ $dirRelease }}/var/boats;
    rm -f {{ $dirReleases }}/last;
    ln -vs {{ $dirRelease }} {{ $dirReleases }}/last;

    echo "Fix right";
    chmod -Rf 777 {{ $dirReleases }}/last/var;
    setfacl -dR -m u:{{ $webUser }}:rwX -m u:$(whoami):rwX {{ $dirReleases }}/last/var;
    setfacl -R -m u:{{ $webUser }}:rwX -m u:$(whoami):rwX var;
    chgrp -Rf www-data {{ $dirReleases }}/last/;
@endtask


###################
# Libs and assets #
###################
@task('composer')
    echo "Task : Composer";
    cd {{ $dirLastRelease }};
    composer install --no-progress --ansi --no-suggest --no-interaction;
@endtask

@task('assets')
    echo "Task : Assets";
    cd {{ $dirLastRelease }};
    ./bin/console assets:install --env={{ $env }};
    ./bin/js-link
    echo "/!\ Compile Webpack : Take a while...";
    ./bin/console maba:webpack:compile --env={{ $env }};
@endtask

@task('npm')
    echo "Task : NPM";
    cd {{ $dirLastRelease }};
    yarn install --no-progress;
@endtask


############
# Database #
############
@task('database')
    echo "Task : Update database";
    cd {{ $dirLastRelease }};
    ./bin/console doctrine:migrations:migrate --env={{ $env }};
    chmod -Rf 777 {{ $dirLastRelease }}/var;
@endtask

@task('fixtures')
    echo "Task : import fixtures";
    cd {{ $dirLastRelease }};
    ./bin/console doctrine:fixtures:load --append --no-interaction --env={{ $env }};
@endtask


############
# Releases #
############
@task('link_current')
    echo "Create link to the current release";
    rm -f {{ $current }};
    ln -sv {{ $dirReleases }}/$(ls {{ $dirReleases }}|grep -e '^[0-9]'|tail -n 1) {{ $current }};
@endtask

@task('rollback')
    rm -f {{ $current }};
    ln -sv {{ $dirReleases }}/$(ls {{ $dirReleases }}|grep -e '^[0-9]'|tail -n 2|head -n 1) {{ $current }};
@endtask

@task('clear')
    echo "Remove old releases";
    cd {{ $dirReleases }}
    ls -r|grep -e '^[0-9]'|tail -n +{{ $nbRelease + 1 }}|xargs rm -rf;
@endtask

#########
# Cache #
#########
@task('cache')
    echo "Clear cache";
    cd {{ $dirLastRelease }};
    ./bin/console cache:clear --env={{ $env }};
    chmod -Rf 777 {{ $dirLastRelease }}/var;
@endtask

@task('warmup')
    echo "Warmup cache";
    cd {{ $dirLastRelease }};
    ./bin/console cache:warmup --env={{ $env }};
    chmod -Rf 777 {{ $dirLastRelease }}/var;
@endtask


##########
# Server #
##########
@task('restart')
    echo "Restart the Websocket server";
    cd {{ $dirLastRelease }};
    ./bin/console redis:flushdb --client=ws_client --no-interaction --env={{ $env }};
    sudo supervisorctl restart battleship;
@endtask

@task('start')
    echo "Start the Websocket server";
    sudo supervisorctl start battleship;
@endtask

@task('stop')
    echo "Stop the Websocket server";
    sudo supervisorctl stop battleship;

    echo "Clear Redis";
    cd {{ $dirLastRelease }};
    ./bin/console redis:flushdb --client=ws_client --no-interaction --env={{ $env }};
@endtask

@task('ping')
    echo "pong (work in {{ $workdir }} on $(hostname))";
@endtask
