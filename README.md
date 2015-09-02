# parse_dot_info

PHP class for parsing .info files into an object containing the key/value pairs. Pairs can then be checked and retrieved.

By default dot_info objects are: 
*	read-only
*	value keys are case __insensitive__
*	and will throw an exception when an error is encountered.

## Flags when instantiating a dot_info object

`dot_info::MATCH_CASE` flag will cause keys to be case sensitive (i.e. tmp != Tmp)
`dot_info::STORE_ERRORS` flag will prevent dot_info from throwing exceptions when errors are encountered, instead the user can call `dot_info::get_last_error()` to find out what the error (if any) was.
`dot_info::READ_INSERT` flag will allow the user to add new key/value pairs but will not allow overwriting of existing pairs.
`dot_info::READ_INSERT_UPDATE` flag will allow the user to add new key/value pairs and overwrite existing pairs.

Flags can be passed in any order, but if the user is expecting the dot_info::STORE_ERRORS behaviour it should be passed as the first flag.

## Public methods

### info_exists() 
`info_exists()` allows users to safely check whether a key/value pair exists before trying to access it.
__NOTE:__ if no parameters are passed, it will return `FALSE`. Otherwise, it will return `TRUE` or `FALSE` depending on whether what you were requesting was found or not.
If your key value pairs are nested within a hierarchy, you'll need to pass the key for each level in the heirarchy (in order) as separate parameters.

### get_info()
`get_info()` returns the value of a key/value pair if it exists or throws an error (Unless in STORE_ERRORS mode). __NOTE:__ if no parameters are passed, all the key/value pairs are returned as an array. If your key value pairs are nested within a hierarchy, you'll need to pass the key for each level in the heirarchy (in order) as separate parameters. __NOTE ALSO:__ By default, get_info() will throw an error if it can't find what you're looking for. This is because the value part of key/value pairs can have both `FALSE` and `NULL` type values thus returning either of those might give a misleading result.

### add_info()
`add_info()` allows users to insert/update key/value pairs. It returns true on success or false on failure. The cause of failure can be determined by calling get_last_error() immediatly after add_info().

### get_info_count()
`get_info_count()` returns the number of available properties stored in the dot_info object. (or just the number top level of properties)

### get_last_error()
`get_last_error()` returns a string containing the last error message (if any) from the last get_info() or add_info() call
 during instantiation and using dot_info::get_info(). However it will return true/false when calling dot_info::info_exists() and dot_info::add_info().

