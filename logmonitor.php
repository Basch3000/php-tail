<?php

class logMonitor
{
	// check-interval in microseconds (500000 = half a second)
	private $iSleep = 500000;
	// log-file to monitor
	private $sFile = "/var/log/auth.log";

	private $iLastModified = 0;
	private $iLastSize = 0;

	public function __construct()
	{

	}

	public function monitor()
	{
		$oFP = fopen($this->sFile, "r");
		while(true)
		{
			clearstatcache(false, $this->sFile);
			$iModified = filemtime($this->sFile);

			if($iModified == $this->iLastModified)
			{
				usleep($this->iSleep);
				continue;
			}

			$this->iLastModified = $iModified;

			$iSize = filesize($this->sFile);
			$iBytesAdded = $iSize - $this->iLastSize;
			$this->iLastSize = $iSize;

			// skip the first time the monitor runs
			if($iBytesAdded == $iSize)
			{
				fseek($oFP, $iSize);
				usleep($this->iSleep);
				continue;
			}

			fseek($oFP, -$iBytesAdded);
			$sAdded = fread($oFP, $iSize);

			// here's the newly added lines
			echo($sAdded);


			usleep($this->iSleep);
		}
	}
}

$oLogMonitor = new logMonitor();
$oLogMonitor->monitor();
