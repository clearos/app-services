
Name: app-services
Epoch: 1
Version: 2.0.24
Release: 1%{dist}
Summary: Services
License: GPLv3
Group: ClearOS/Apps
Packager: Tim Burgess
Vendor: Tim Burgess
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
The Services app is used to control the system daemons and their running state and provides a useful overview. You can also control which services start up at boot time.

%package core
Summary: Services - Core
License: GPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-base-core >= 1:1.6.5

%description core
The Services app is used to control the system daemons and their running state and provides a useful overview. You can also control which services start up at boot time.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/services
cp -r * %{buildroot}/usr/clearos/apps/services/


%post
logger -p local6.notice -t installer 'app-services - installing'

%post core
logger -p local6.notice -t installer 'app-services-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/services/deploy/install ] && /usr/clearos/apps/services/deploy/install
fi

[ -x /usr/clearos/apps/services/deploy/upgrade ] && /usr/clearos/apps/services/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-services - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-services-core - uninstalling'
    [ -x /usr/clearos/apps/services/deploy/uninstall ] && /usr/clearos/apps/services/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/services/controllers
/usr/clearos/apps/services/htdocs
/usr/clearos/apps/services/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/services/packaging
%dir /usr/clearos/apps/services
/usr/clearos/apps/services/deploy
/usr/clearos/apps/services/language
/usr/clearos/apps/services/libraries
