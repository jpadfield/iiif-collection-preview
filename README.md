# IIIF Collection Preview

An experimental tool which searches through a provided Version 3 [IIIF Collection](https://iiif.io/api/presentation/3.0/#51-collection) or [IIIF Manifest](https://iiif.io/api/presentation/3.0/#52-manifest) file identifies all of the images referenced within the file and within the any nested/referenced files and then loads them into [OpenSeadragon](https://openseadragon.github.io/).

## Usage

* The link to a IIIF document needs to be given as an GET variable called `uri`
* An optional `limit` variable can also be provided to limti the total number of images loaded (there is currently a hard limit of 2000 images included in the code as the loading times increase quite a bit as the total number of images increases).
* Example: https://research.ng-london.org.uk/cv/?uri=https://research.ng-london.org.uk/iiif-projects/json/ng-projects.json&limit=250

# Acknowledgement
This project was developed and tested as part of the work of the following projects:

## The AHRC Funded [IIIF - TANC](https://tanc-ahrc.github.io/IIIF-TNC) project
<img height="64px" src="https://github.com/jpadfield/simple-site/blob/master/docs/graphics/TANC - IIIF.png" alt="IIIF - TNC">
