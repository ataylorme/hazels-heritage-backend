#!/bin/bash

# Variables
BUILD_DIR=$(pwd)
txtred=$(tput setaf 1) # Red
txtgrn=$(tput setaf 2) # Green
txtylw=$(tput setaf 3) # Yellow
txtblu=$(tput setaf 4) # Blue
txtpur=$(tput setaf 5) # Purple
txtcyn=$(tput setaf 6) # Cyan
txtwht=$(tput setaf 7) # White
txtrst=$(tput sgr0) # Text reset.

COMMIT_MESSAGE="$(git show --name-only --decorate)"

cd $HOME

# If the droplet directory does not exist
if [ ! -d "$HOME/digital-ocean" ]
then
	# Clone the droplet directory
	echo -e "\n${txtylw}Cloning droplet into $HOME/digital-ocean  ${txtrst}"
	scp -r root@${DROPLET_IP}:/srv/users/serverpilot/apps/backend $HOME/digital-ocean
fi

cd digital-ocean

# Delete the web and vendor subdirectories if they exist
if [ -d "$HOME/digital-ocean/public" ]
then
	# Remove it
	echo -e "\n${txtylw}Removing $HOME/digital-ocean/public ${txtrst}"
	rm -rf $HOME/digital-ocean/public
fi
if [ -d "$HOME/digital-ocean/vendor" ]
then
	# Remove it
	echo -e "\n${txtylw}Removing $HOME/digital-ocean/vendor ${txtrst}"
	rm -rf $HOME/digital-ocean/vendor
fi

mkdir -p public
mkdir -p vendor

echo -e "\n${txtylw}Rsyncing $BUILD_DIR/web to public ${txtrst}"
rsync -a $BUILD_DIR/web/* ./public/

echo -e "\n${txtylw}Copying $BUILD_DIR/wp-cli.yml ${txtrst}"
cp $BUILD_DIR/wp-cli.yml .

echo -e "\n${txtylw}Rsyncing $BUILD_DIR/vendor ${txtrst}"
rsync -a $BUILD_DIR/vendor/* ./vendor/

echo -e "\n${txtylw}Rsyncing $BUILD_DIR/config ${txtrst}"
rsync -a $BUILD_DIR/config/* ./config/

# Some plugins have .svn directories, nuke 'em
echo -e "\n${txtylw}Removing all '.svn' directories${txtrst}"
find . -name '.svn' -type d -exec rm -rf {} \;

# Remove node_modules left by gulp/grunt
echo -e "\n${txtylw}Removing all 'node_modules' directories${txtrst}"
find . -name 'node_modules' -type d -exec rm -rf {} \;

# Deploy to droplet
if [ $CIRCLE_BRANCH = "master" ]
then
	scp -r $HOME/digital-ocean root@${DROPLET_IP}:/srv/users/serverpilot/apps/backend/
fi