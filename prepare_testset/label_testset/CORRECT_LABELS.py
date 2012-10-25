#!/usr/bin/env python

file_name = '_'+'test.ctf.emoWrd.2011-07-04.07061740'+'.csv'

import csv
import os

testset = csv.reader(open(file_name, 'rb'), delimiter=',', quotechar='"')
l_testset = csv.writer(open('_'+file_name, 'wb'), delimiter=',', quotechar='"', quoting=csv.QUOTE_NONNUMERIC)

for tweet in testset:
	print "Tweet:\n\n" + tweet[1] + "\n\nCatagory:\n\n" + tweet[0]
	print "\n\n1  POSITIVE\t2  NEGATIVE\n"
	op=raw_input("Enter choice : ")
	print op
	if op == '1':
		if tweet[0] == 'negative':
			tweet[0] = 'positive'
	elif op == '2':
		if tweet[0] == 'positive':
			tweet[0] = 'negative'
	
	l_testset.writerow((tweet[0], tweet[1]))
	
	os.system('clear')
