import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { NotfoundComponent } from './components/notfound/notfound.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { EstadisticasComponent } from './components/estadisticas/estadisticas.component';
import { SearchingComponent } from './components/searching/searching.component';
import { JuegoComponent } from './components/juego/juego.component';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'dashboard', component: DashboardComponent},
    { path: 'estadisticas', component: EstadisticasComponent},
    { path: 'search', component: SearchingComponent },
    { path: 'juego', loadComponent: () => import('./components/juego/juego.component').then(j => j.JuegoComponent) },
    { path: '**', component: NotfoundComponent}
];
