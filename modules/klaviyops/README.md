# PrestaShop

## Making updates
1) Follow Klaviyo's standard process of making a pull request and getting it reviewed before merging.
2) If your change requires running a method for new functionality (anything done on install e.g. create/update DB table, add an admin tab) you'll need to add an upgrade file. [PrestaShop documentation available here](https://devdocs.prestashop.com/1.7/modules/creation/enabling-auto-update/).
3) Update CHANGELOG.md. Details on formatting the changelog (including categorizing changes) can be found here: [keepachangelog.com](https://keepachangelog.com/en/1.0.0/)
    1) If this is a change that will not immediately get sent along to PrestaShop i.e. not a version update:
        1) Add any changes under the [`[Unreleased]`](https://github.com/klaviyo/prestashop_klaviyo/compare/1.0.1...HEAD) section. This will be a comparision of the most recent commits to the latest tagged version.
    2) If this is a version update:
        1) Make sure to increment the version in two places:
            1) KlaviyoPs class constructor method (klaviyops.php)
            2) config.xml file
        2) Add a new version between `[Unreleased]` and the most recent version. Include the incremented version number following [semantic versioning](https://semver.org/spec/v2.0.0.html) practices and the date. Add your changes under this version.
        3) Move any unreleased changes into your version update under the appropriate categories.
        4) Update the `[Unreleased]` link to point from your new version to HEAD e.g. if you're updating to version 1.0.2 you'd update the link from `1.0.1...HEAD` to `1.0.2...HEAD`.
        5) Add a link to your new version. The tag won't yet exist but you can create a link to the tag you will create shortly. Follow the pattern of previous links.
4) Upon approval merge your changes into master.
    1) If this is a version update:
        1) Checkout the master branch locally, make sure to pull down any changes that were just merged.
        2) Use `git log` to find the merge commit's checksum.
        3) Tag this commit with the version you just incremented: `git tag -a {version} aeb8c682cebe7acee94506d3e4bfff2e5755e8c1` or just use `git tag -a {version}`.
        4) Push the tag to the remote repository: `git push origin 1.0.1` replacing with the version you've just tagged.

## Manually uploading the module to PrestaShop
1) Download the zipped repo.
2) Extract and rename directory to klaviyops (PrestaShop requires the zipped file name and the directory to match).
3) Re-compress the directory ensuring the zipped file is named klaviyops.zip. For submission to PrestaShop please make sure to exclude ancillary files like `.git`. You can do this via commandline by running the following command: `zip -r klaviyops.zip klaviyops/ -x "*.git*"`
4) Navigate to the Modules tab in PrestaShop and click "Upload a Module". Follow prompts.
5) Navigate to the module within PrestaShop and configure accordingly.
