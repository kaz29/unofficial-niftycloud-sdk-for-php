#!/bin/sh
echo "setup started. Now updating packages..." > /root/_setup.log
/bin/cat <<EOF > /etc/rc.local
apt-get update
apt-get -y upgrade
apt-get install -y curl git-core
curl https://raw.github.com/gist/1576823/7859b65f36ae4c79b8c3298ec683879d95e3955c/cakephp2.sh | sh | tee /root/_setup.log
/bin/sed -i.orig -e "s/apt/#apt/g" /etc/rc.local
/bin/sed -i.orig -e "s/curl https/#curl https/g" /etc/rc.local
/bin/sed -i.orig -e "s/\/bin\/sed/#\/bin\/sed/g" /etc/rc.local
exit 0
EOF

