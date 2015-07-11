Librarian
=========

The use of the [librarian-vde](https://github.com/martyn82/librarian-vde) is recommended when working on this project.

## Using SonarQube for analysis
There is a Phing task for this in the build file `$ bin/phing analyse`

However, when installing the SonarQube service as a docker container (you can do this by running `$ bin/phing sonarqube:start`) the installation of SonarQube does not yet support PHP.

This means you have to browse to http://localhost:9000 (or replace 'localhost' with the IP of your VDE), and install the plugin manually. Then restart the docker container and it will work.
To do this automatically is still under development by the maintainer of the SonarQube docker image (https://registry.hub.docker.com/_/sonarqube/).
