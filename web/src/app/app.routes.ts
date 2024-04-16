import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { NotfoundComponent } from './components/notfound/notfound.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { EstadisticaComponent } from './components/estadistica/estadistica.component';
import { SearchingComponent } from './components/searching/searching.component';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'dashboard', component: DashboardComponent},
    { path: 'estadisticas', component: EstadisticaComponent},
    { path: 'search', component: SearchingComponent },
    { path: '**', component: NotfoundComponent}
];
