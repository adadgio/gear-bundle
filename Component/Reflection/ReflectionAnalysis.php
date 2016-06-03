<?php

namespace Adadgio\GearBundle\Component\Reflection;

/**
 * Reflection analysis utility, fitted for
 * kernel request event listeners and hydrating
 * parameters in the controller automatically.
 */
class ReflectionAnalysis
{
    /**
     * @var array Array of \\ReflectionParameter(s)
     */
    protected $methodParameters;

    /**
     * Sets the base controller and input.
     *
     * @param  string Controller and metho "Namespaced\Controller::methodAction"
     * @return object \ReflectionAnalysis
     */
    public function of($controllerAndMethod)
    {
        // separate controller namespace from method name
        $exploded = explode('::', $controllerAndMethod);

        if (count($exploded) === 2) {
            // there is a valid method
            $reflectionMethod = new \ReflectionMethod($exploded[0], $exploded[1]);

            // create an array of \ReflectionParameter
            $this->methodParameters = $reflectionMethod->getParameters(); // an array of \ReflectionParameter(s)

        } else {
            // else there is no method
            $this->methodParameters = array();
        }
    }

    /**
     * Find the method argument/parameter declared variable name
     * that is type hinted with a given class name.
     *
     * @param  string Type hint to look for
     * @return string Or false if type hint not found
     */
    public function findTypeHintedArgName($typeHint)
    {
        // loop through each method parameters
        foreach ($this->methodParameters as $reflectionParam) {
            // each $parameter is a instance if \ReflectionParameter
            // we get the typehint short class name
            $typeHintShortName = $this->getHintShortClassName($reflectionParam);

            // return the parameter declared variable name (without the "$")
            if ($typeHint === $typeHintShortName) {
                return $reflectionParam->getName();
            }
        }

        return false;
    }

    /**
     * Get the short class name version of the type hint class.
     *
     * @param  object \ReflectionParameter
     * @return string Short type hint class name
     */
    private function getHintShortClassName(\ReflectionParameter $reflectionParam)
    {
        // get the full type hint namespace "Namespace\Blah\TypehintClass"
        $reflectionClass = $reflectionParam->getClass();

        // when there is no type hint, class is null
        if (!$reflectionClass instanceof \ReflectionClass) {
            // typehing could not be a class like "array", but in that case still skip
            return false;
        }

        $namespace = $reflectionClass->getName();

        // then just return the type hint class short name
        $namespaceParts = explode('\\', $namespace);
        return end($namespaceParts);
    }
}
