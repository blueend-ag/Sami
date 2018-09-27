<?php

/*
 * This file is part of the Sami utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sami\Parser\ClassVisitor;

use Sami\Parser\ClassVisitorInterface;
use Sami\Reflection\ClassReflection;

/**
 * Treat mixin as trait
 * @package Sami\Parser\ClassVisitor
 */
class MixinClassVisitor implements ClassVisitorInterface
{
    public function visit(ClassReflection $class)
    {
    	if($mixin = $class->getTags('mixin')){
				$name = $mixin[0][0];
				$name = $this->resolveMixin($class,$name);
    		$class->addTrait($name);
			}
    }

    protected function resolveMixin(ClassReflection $class, $alias){
			// FQCN
			if ('\\' == substr($alias, 0, 1)) {
				return $alias;
			}
			// an alias defined by a use statement
			$aliases = $class->getAliases();

			if (isset($aliases[$alias])) {
				return $aliases[$alias];
			}

			// a class in the current class namespace
			return $class->getNamespace().'\\'.$alias;
		}
}
