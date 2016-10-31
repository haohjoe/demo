<?php

/**
 * FileManager class file
 *
 * @author zhanglu@camera360.com
 * @date 2014/06/19
 *
 */
class FileManager
{

    /**
     * @brief 检查文件的Md5值
     * 
     * @param string $strFile 文件地址
     * @param string $strMd5 Md5值
     * @return boolean
     */
    public static function checkFileMd5($strFile, $strMd5)
    {
        $strFileMd5 = md5_file($strFile);
        $intRet = strcasecmp($strFileMd5, $strMd5);
        if ($intRet !== 0) {
            return false;
        }
        
        return true;
    }

    /**
     * @brief wget方式下载文件，如果原文件已存在，重命名
     * 
     * @param string $strUrl 线上文件目录，不包括文件名
     * @param string $strName 文件名
     * @param string $strToDir 下载后存放的目录
     * @param integer $intCutDirs 需要省略的目录级数
     * @param string $strSuffix 重命名后的文件后缀名
     * @return string $strRet 下载后的文件地址
     */
    public static function download($strUrl, $strName, $strToDir, $intCutDirs, $strSuffix = '.bk')
    {
        $strRet = $strToDir . '/' . $strName;
        if (is_file($strRet) && ! rename($strRet, $strRet . $strSuffix)) {
            return false;
        }
        
        $strUrl .= '/' . $strName;
        self::wget($strUrl, $strToDir, $intCutDirs);
        
        if (! is_file($strRet)) {
            return false;
        }
        
        return $strRet;
    }

    /**
     * @brief wget方式下载文件
     * 
     * @param string $strUrl 线上文件地址
     * @param string $strToDir 下载后存放的目录
     * @param integer $intCutDirs 需要省略的目录级数
     * @return string $strRet
     */
    public static function wget($strUrl, $strToDir, $intCutDirs)
    {
        $strCmd = 'wget -r ' . $strUrl . ' -P ' . $strToDir . ' -nH -nd --cut-dirs=' . $intCutDirs . ';';
        $strRet = self::execShellCmd($strCmd);
        
        return $strRet;
    }

    /**
     * @brief 删除文件或目录
     * 
     * @param string $strFileName
     *            要删除的文件名或目录名
     * @return string $strRet
     */
    public static function rm($strFileName)
    {
        if (! file_exists($strFileName)) {
            return true;
        }
        $strCmd = 'rm -rf ' . $strFileName . ';';
        $strRet = self::execShellCmd($strCmd);
        if (file_exists($strFileName)) {
            return false;
        }
        
        return true;
    }

    /**
     * @brief 修改目录权限
     * 
     * @param string $strFileName 目标文件名或目录名
     * @param string $mode 权限
     * @return string $strRet
     */
    public static function chmod($strFileName, $mode)
    {
        $strCmd = 'chmod -R ' . $mode . ' ' . $strFileName . ';';
        $strRet = self::execShellCmd($strCmd);
        
        return $strRet;
    }

    public static function cp($strSrc, $strDesc, $isDir)
    {
        if ($isDir) {
            $strDir = $strDesc;
        } else {
            $strDir = basename($strDesc);
        }
        if (! file_exists($strDir)) {
            if (self::mkDir($strDir) === false) {
                return false;
            }
        } elseif (! is_dir($strDir)) {
            return false;
        }
        $strCmd = 'cp -rf ' . $strSrc . ' ' . $strDesc . ';';
        $strRet = self::execShellCmd($strCmd);
        
        return $strRet;
    }

    /**
     * @brief 执行Shell命令
     * 
     * @param string $strCmd 命令
     * @return string $strRet
     */
    public static function execShellCmd($strCmd)
    {
        $resFile = popen($strCmd, 'r');
        $strRet = stream_get_contents($resFile);
        pclose($resFile);
        
        return trim($strRet);
    }

    /**
     * @brief 创建目录
     * 
     * @param string $strDir 需要创建的目录地址
     * @return string 完整的目录，如：/home/worker/data/res/mis
     */
    public static function mkDir($strDir)
    {
        if (is_dir($strDir)) {
            return $strDir;
        }
        if (false === mkdir($strDir, 0777, true)) {
            return false;
        }
        if (is_dir($strDir)) {
            return $strDir;
        }
        
        return false;
    }

    /**
     * @brief 将字符串清理成符合Linux目录的地址
     * 
     * @param string $strDir 清理前的字符串，如：$strDir = ' /data/res/mis/ ';
     * @return string $strDir 符合规范的目录，如：$strDir = '/data/res/mis';
     */
    public static function cleanDir($strDir)
    {
        $strDir = rtrim(rtrim(trim($strDir), '/'), '\\');
        return $strDir;
    }

    public static function saveUploadFile($arrFile, $strFileName, $arrTypes = null, $intMaxSize = 10485760)
    {
        if ($arrTypes !== null && ! in_array(strtolower($arrFile['type']), $arrTypes)) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[invalid file type] type[' . $arrFile['type'] . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        if ($arrFile['size'] > $intMaxSize) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[file size out of limit] size[' . $arrFile['size'] . '] max[' . $intMaxSize . 'B]', Errno::INTERNAL_SERVER_ERROR);
        }
        if ($arrFile['error'] > 0) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[error occur] error[' . $arrFile['error'] . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        self::rm($strFileName);
        if (file_exists($strFileName)) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[file exist and delete fail] file[' . $strFileName . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        $strDir = dirname($strFileName);
        if (! self::mkDir($strDir)) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[mkdir fail] dir[' . $strDir . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        
        if (false === move_uploaded_file($arrFile['tmp_name'], $strFileName)) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . ']' . ' msg[move_uploaded_file fail] dir[' . $strDir . ']', Errno::INTERNAL_SERVER_ERROR);
        }
        self::chmod($strFileName, 'a+rw');
        
        return true;
    }

    public static function readFileToArray($file)
    {
        if (empty($file) || ! is_readable($file)) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . '] file[' . $file . ']' . ' msg[can not read $file]', Errno::INTERNAL_SERVER_ERROR);
        }
        $arrLines = file($file);
        if (false === $arrLines) {
            throw new Exception('file[' . __FILE__ . '] line[' . __LINE__ . '] class[' . __CLASS__ . '] func[' . __FUNCTION__ . ']' . ' time[' . time() . '] file[' . $file . ']' . ' msg[read file fail]', Errno::INTERNAL_SERVER_ERROR);
        }
        $arrRet = array();
        foreach ($arrLines as $strLine) {
            $strLine = trim($strLine);
            if (empty($strLine)) {
                continue;
            }
            $arrRet[] = $strLine;
        }
        
        return $arrRet;
    }
}
