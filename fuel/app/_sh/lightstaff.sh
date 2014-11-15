# 文字コードutfに
nkf -w --overwrite /lightstaff_csv/*
echo "UTF-8 done"
# "false" => "0", "true" => "1", add [,] to import valid .csv file 
find /lightstaff_csv -name *.csv | xargs sed -i '' s/\"false\"/\"0\"/g
echo "false done"
find /lightstaff_csv -name *.csv | xargs sed -i '' s/\"true\"/\"1\"/g
echo "true done"
find /lightstaff_csv -name *.csv | xargs sed -i '' 's/$/,/g'
find /lightstaff_csv -name *.csv | xargs sed -i '' -e s///g
#  Ctrl + v と Ctrl + m
echo "done"
