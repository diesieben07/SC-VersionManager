<?php
namespace de\take_weiland\sc_versions;

class HelpAction extends AbstractAction {

    public function perform() {
        echo trim(file_get_contents(__FILE__, false, null, __COMPILER_HALT_OFFSET__ + 3));
    }
}
__halt_compiler(); ?>
Valid Parameters: "action" - the action to perform, see below. If omitted or invalid, prints this page.

Valid actions: "maven" - extracts version information from a Maven repository.
                         Required parameters: "repo"              - The Maven repository URL (e.g. "http://maven.example.com")
                                              "group"             - The Group ID (e.g. "com.example.some_software")
                                              "artifact"          - The Artifact ID (e.g. "SomeSoftware")
                         Optional parameters: "additionalInfoURL" - An URL pattern that points to a JSON file which provides the fields
                                                                    "patchNotes" and "dependencies" (e.g. "http://example.com/versioninfo%s.json").
                                                                    The optional "%s" in the URL will be replaced with the version.
                                                                    The JSON must be structured like this (with "*version*" representing the version):
                                                                    {
                                                                        "*version*": {
                                                                            "dependencies": "...",
                                                                            "patchNotes": "..."
                                                                        },
                                                                        "*someOtherVersion*": { ... }
                                                                    }
                                              "version"           - Select a single version to print the info for.
                                              "param"             - Select a single parameter to get (e.g. "patchNotes"). Only valid together with "version".
                                                                    This changes the output from being JSON to a single string value.
                                              "pretty"            - Pretty-print the JSON. Only used if "param" is not present.
                                              "indexed"           - If set to anything other than "true" (the default) changes the output from a
                                                                    version-indexed associative array to a flat array without indexes.
                                                                    Only used if "version" is not present.