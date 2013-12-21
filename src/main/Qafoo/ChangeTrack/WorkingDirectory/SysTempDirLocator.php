<?php

namespace Qafoo\ChangeTrack\WorkingDirectory;

class SysTempDirLocator
{
    public function getTempDir()
    {
        return sys_get_temp_dir();
    }
}
