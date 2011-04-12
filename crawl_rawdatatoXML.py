#!/usr/bin/python2.7
#
# Crawl stats - where file to XML
# By: Chris Brasington
#
import getopt, sys, urllib,  os, urllib2
output = "stats.xml"

# helper command-line options
def usage():
	print "Crawl Stats : Rawdata to XML \n"
	print "-h --help \tPrint this message." 
	print "-u --username \tSpecify Username(s), comma seperated" 
	print "\t\t -u user1"
	print "\t\t -u user1,user2,user3"
	print "-o --output \tSpecify Output File\n" 

def main():
	
	# try options
    try:
        opts, args = getopt.getopt(sys.argv[1:], "hu:vo:v", ["help", "username=", "output="])
    except getopt.GetoptError, err:
        # print help information and exit:
        print str(err) # will print something like "option -a not recognized"
        usage()
        sys.exit(2)

	users = None
	global output

	# parse options
    for o, a in opts:
		if o in ("-h", "--help"):
			usage()		
		elif o in ("-u", "--username"):
			users = a
		elif o in ("-o", "--output"):
				output = a
				print "Stored in file: ",output

	# make sure user(s) are specified		
    try: 
        users
    except NameError:
        print '\nMust specify users!'
        usage()
        sys.exit(0)
        
    # parse XML of users
    parseXML(users.split(','))

# parse XML
def parseXML(users):
	
	# header
	f=open(output, 'w')
	f.write( '<?xml version="1.0" encoding="utf-8"?>\n' )
	f.write( "<users>\n" )
	
	# for each user
	for u in users: 
		f.write( '\t<'+u+'>\n' )
	
		# file read
		url=urllib2
		req = urllib2.Request('http://crawl.akrasiac.org/rawdata/'+u+'/'+u+'.where')

		try: 
			# valid / online check ?
			urllib2.urlopen(req)
			
			data = urllib.urlopen('http://crawl.akrasiac.org/rawdata/'+u+'/'+u+'.where').read().split(':')
			data[-1] = data[-1].rstrip()

			# write each stat
			for stat in data:
				if len(stat) > 1 :
					stat = stat.split('=')
				
					f.write( '\t\t'+'<'+stat[0]+'>\n' )
					f.write( '\t\t\t'+stat[1]+'\n' )
					f.write( '\t\t'+'</'+stat[0]+'>\n' )
				
			f.write( '\t</'+u+'>\n' )
		
		except url.URLError, e:
			f.write( '\t</'+u+'>\n' )
			print "Error with username: "+u
			print e
		
	# close
	f.write( "</users>\n" )

if __name__ == "__main__":
    main()
