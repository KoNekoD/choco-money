import {Outlet} from 'react-router-dom';
import {observer} from 'mobx-react-lite';
import {Toaster} from 'react-hot-toast';

export const Layout = observer(() => {
  return (
      <main className="">
          <Toaster/>
          <Outlet/>
          {/*<Sidebar/>*/}
      </main>
  );
});
