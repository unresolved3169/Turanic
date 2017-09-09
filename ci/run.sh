#!/bin/sh

PHP_BINARY="php"

while getopts "p:" OPTION 2> /dev/null; do
	case ${OPTION} in
		p)
			PHP_BINARY="$OPTARG"
			;;
	esac
done

./ci/lint.sh -p "$PHP_BINARY"

if [ $? -ne 0 ]; then
	echo Lint taraması hatalı.
	exit 1
fi

echo -e "\nversion\nms\nstop\n" | "$PHP_BINARY" -dphar.readonly=0 src/pocketmine/PocketMine.php --no-wizard --disable-ansi --disable-readline --debug.level=2
if ls plugins/Turanic/Turanic*.phar >/dev/null 2>&1; then
    echo Sunucu pharı başarıyla oluşturuldu.
else
    echo Phar oluşturulmadı.
    exit 1
fi