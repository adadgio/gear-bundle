<?php

namespace Adadgio\GearBundle\Component\Filesystem;

/**
 * Helps create journalized directories from
 * a base directory. Usefull for upload directories
 * because its bad to have hundred of files or dirs
 * inside on directory (UNIX limitations, FTP access, etc)
 *
 * By convention, all directories string pathes are trimmed of the trailing slash.
 */
class Journalizer
{
    /**
     * @var strinf Final journalized directory path
     */
    protected $dir;

    /**
     * @var string Original base path to the
     * top journalized directory
     */
    protected $basepath;

    /**
     * @var mixed Input object/resource identifier
     */
    protected $identifier;

    /**
     * @var string JOurnalized dir directory prefix
     * Warning: Never change this!
     */
    const PREFIX = 'dir';

    /**
     * @var string Number of files per directory (minus 1 in reality)
     * Warning: Never change this!
     */
    const FILES_PER_DIR = 300;

    /**
     * Class constructor.
     *
     * @param string Start directory basepath
     * @param string Object (entity) identifier on wich the final path will deducted
     */
    public function __construct($basepath, $identifier)
    {
        $this->identifier = $identifier;
        $this->basepath = rtrim($basepath, '/');
        $this->dir = $this->compute($this->identifier, $this->basepath);
    }

    /**
     * Get final journalized directory path. Always returns
     * a path with no end trailing slash on the right.
     *
     * @return string Directory server path
     */
    public function getDir()
    {
        return $this->dir;
    }
    
    /**
     * Compute the final server directory path.
     *
     * @param  integer Object id
     * @param  string  Base assets/docs path
     * @return string  Journalized dir full path
     */
    protected function compute($identifier, $basepath)
    {
        $identifier = (null === $identifier) ? 0 : $identifier;

        // example calculation, group folders 300 by 300 (i.e. static::FILES_PER_DIR)
        // $nth  = ceil($identifier / 300);
        // $dirA = ((300*$nth) - 300);
        // $dirB = ($dirA + 300) - 1;
        $nth  = (int) ceil($identifier / static::FILES_PER_DIR);
        $dirA = ((static::FILES_PER_DIR * $nth) - static::FILES_PER_DIR);

        // handle exception when identifier is "0" or null, in which case dirA becomes negative
        $dirA = ($dirA < 0) ? 0 : $dirA;

        // the dirB can be safely created
        $dirB = abs(($dirA + static::FILES_PER_DIR) - 1);

        $relativepath = static::PREFIX . $dirA . '-' . $dirB;
        return $basepath . '/' . $relativepath . '/' . $identifier;
    }
}
