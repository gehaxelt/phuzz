#
# Cloned and slightly modified from https://github.com/umarfarook882/carbonator
#


# Created by Blake Cornell, CTO, Integris Security LLC
# Integris Security Carbonator - Beta Version - v1.2
# Released under GPL Version 2 license.
#
# See the INSTALL file for installation instructions.
# 
# For more information contact us at carbonator at integrissecurity dot com
# Or visit us at https://www.integrissecurity.com/

from burp import IBurpExtender
from burp import IHttpListener
from burp import IScannerListener
from java.net import URL
from java.io import File

from phuzz import Phuzz
import time
import json
import sys
import StringIO
import os

class BurpExtender(IBurpExtender, IHttpListener, IScannerListener):

	def registerExtenderCallbacks(self, callbacks):
		self._callbacks = callbacks
		self._callbacks.setExtensionName("Carbonator")
		self._helpers = self._callbacks.getHelpers()
		self.clivars = None
		self.fuzzer_config = None

		self.scanner_results=[]
		self.packet_timeout=0
		self.scan_timeout = 300 # 180 # Configure the timeout after which BurpSuite will stop the scan and generate the report. In our tests, 300s was more than enough time.
		self.start_time = None
		self.phuzz = Phuzz(self)
		self.base_output_folder = "/home/burp/data/output"
		try:
			os.mkdir(self.base_output_folder)
		except:
			pass

		try:
			cli = self._callbacks.getCommandLineArguments()
			print(cli)
			self.fuzzer_config_name = cli[0]
			self.plugin_name = cli[1] if len(cli) > 1 else ""
			self.phuzz.load_config()
			self.phuzz.load_request_data()
			self.url = URL(self.phuzz.config['target'])

			self.output_folder = self.base_output_folder + "/" + self.plugin_name
			try:
				os.mkdir(self.output_folder)
			except:
				pass

			print "Initiating Carbonator Against: ", str(self.url)
			if self._callbacks.isInScope(self.url) == 0:
				self._callbacks.includeInScope(self.url)

			
			for c in self.phuzz.generate_initial_candidates():
				print("Using candidate: ", str(c))
				request = self.generateRequest(c)
				print("Queueing request:", request)
				self._callbacks.doActiveScan(self.url.getHost(),self.getPort(),0,request)

			self.start_time = int(time.time())
			self.last_packet_seen= int(time.time())


			while (int(time.time()) - self.start_time) <= self.scan_timeout:
				print("Waiting..." + str(self.scan_timeout - (int(time.time()) - self.start_time)))
				time.sleep(1)

			print "Removing Listeners"
			self._callbacks.excludeFromScope(self.url)

			print("Generating reports")
			self.generateReport('HTML')
			self.generateReport('XML')
		except Exception as e:
			print("error: ", e)

		self._callbacks.exitSuite(False)
			
		return

	def generateRequest(self, params):
		req = self.phuzz.prepare_request(params)
		sb = StringIO.StringIO()
		sb.write(req.method)
		sb.write(" ")
		sb.write(req.path_url)
		sb.write(" HTTP/1.0\n")
		sb.write("Host: " + self.getHost() + "\n")
		for k,v in req.headers.items():
			sb.write(k + ": " + v)
			sb.write("\n")
		sb.write("\n")
		sb.write(req.body)

		return sb.getvalue()

	def getHost(self):
		if self.url.getPort() < 0:
			return self.url.getHost()
		else:
			return self.url.getHost() + ":" + str(self.url.getPort())
	def getPort(self):
		if self.url.getPort() < 0:
			return 80
		else:
			return self.url.getPort()

	def generateReport(self, format):
		file_name = self.fuzzer_config_name + '_report.'+format.lower()
		file_name = self.output_folder + "/" + file_name.replace("/app/configs/","").replace("/","_")
		self._callbacks.generateScanReport(format,self._callbacks.getScanIssues(str(self.url)),File(file_name))

		return
