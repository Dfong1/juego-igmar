import { Component, OnInit } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { UserService } from '../../services/user.service';
import { User } from '../../Interfaces/user-interface';
import { EstadisticaService } from '../../services/estadistica.service';
import { Estadistica } from '../../Interfaces/estadistica';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-estadisticas',
  standalone: true,
  imports: [NavbarComponent, CommonModule],
  templateUrl: './estadisticas.component.html',
  styleUrl: './estadisticas.component.css'
})
export class EstadisticasComponent implements OnInit {

  constructor( private us: UserService, private es: EstadisticaService ) {}

  public user: User = {
    id: 0,
    email: "",
    name: "",
    codigoVerificadO: false,
    created_at: "",
    is_active: false,
    updated_at: ""
  }

  public estadistica: Estadistica = {
    msg: "",
    data: [{
      partida: 0,
      rival_id: 0,
      rival_name: "",
      user_id: 0,
      user_name: "",
      rival_ships_remaining: 0,
      user_ships_remaining: 0
    }]
  }

  ngOnInit(): void {
    this.us.getData().subscribe(
      (response) => {
        this.user = response
      }
    )

    this.es.getEstadistica().subscribe(
      (response) => {
        console.log(response)

        this.estadistica = response

        response.data.forEach((res:any, index) => {
          if(res.partida == this.user.id){
            this.estadistica.data[index].user_name = "Ganaste"
          }
          else{
            this.estadistica.data[index].user_name = "Perdiste"
          }
        })
      }
    )

  }

}
