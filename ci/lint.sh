#!/bin/sh

PHP_BINARY="php"

while getopts "p:" OPTION 2> /dev/null; do
	case ${OPTION} in
		p)
			PHP_BINARY="$OPTARG"
			;;
	esac
done

echo PHP lint taraması başlıyor...

OUTPUT=`find ./src/pocketmine -name "*.php" -print0 | xargs -0 -n1 php -l`

if [ $? -ne 0 ]; then
	echo $OUTPUT | grep -v "Hata yok"
	exit 1
fi

echo Lint taraması başarıyla tamamlandı!