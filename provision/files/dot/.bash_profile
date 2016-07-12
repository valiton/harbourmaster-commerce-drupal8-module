export LC_ALL=en_US.UTF-8
export LC_CTYPE=en_US.UTF-8

source ~/.bash_git
source ~/.bashrc

PATH=$PATH:$HOME/bin:/vagrant_data/vendor/bin
export PATH

if [ -d /vagrant_data/docroot ]; then
    cd /vagrant_data/docroot
else
    cd /vagrant_data
fi