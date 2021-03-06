Librarian
=========

The use of the [librarian-vde](https://github.com/martyn82/librarian-vde) is recommended when working on this project.

## Using SonarQube for analysis
There is a Phing task for this in the build file `$ bin/phing analyse`

However, when installing the SonarQube service as a docker container (you can do this by running `$ bin/phing sonarqube:start`) the installation of SonarQube from the image provided is not fully completed yet.

Completing the SonarQube installation is done by the following steps:
- Run the SonarQube container (if it isn't already)
- Browse to http://localhost:9000 (or replace 'localhost' by the IP of your VDE)
- Log in to SonarQube (default credentials), and browse to the Sonar Update Center
- Under Available Plugins find and install: PHP, JavaScript
- Stop and Start the container to activate the changes
 
As an alternative final step, you can export the container to keep it in case of loss. `$ docker export <CONTAINER ID> > sonarqube.tar`
