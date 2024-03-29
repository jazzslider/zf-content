This distributable content module for Zend Framework is intended to be
installable in any Zend Framework project, particularly those utilizing
the default project structure provided through the use of Zend_Tool.  To
install the module, please do the following:

1. Copy this "content" directory into your project's "modules" directory.
2. Install the database schemas provided in the "schemas" directory.  These
   schemas are provided in the format expected by dbdeploy; in general, you
   will only need to run the SQL statements prior to the @UNDO line.  Just
   remember that if there are multiple files in "schemas/deltas", they will
   need to be installed in their numbered order.
3. Make sure your application's bootstrap configuration includes the
   Zend_Application_Resource_Modules resource.
4. Optionally, you can enable certain filters for your content's body text
   by adding a few entries to your application's bootstrap configuration.
   For example, to allow your content authors to choose from the SafeHtml
   and AllHtml filters included in the module, your application's config
   should include the following:

   content.outputFilters.filter1 = "Content_View_Filter_SafeHtml"
   content.outputFilters.filter2 = "Content_View_Filter_AllHtml"

   Keep in mind that these filters have certain dependencies that must be
   downloaded separately and installed in your include_path; otherwise, you'll
   get a fatal error when they're require_once'd.
