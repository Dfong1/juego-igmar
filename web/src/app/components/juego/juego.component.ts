import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-juego',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './juego.component.html',
  styleUrl: './juego.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class JuegoComponent implements OnInit {

  board: string[][] = [];

  constructor() { }

  ngOnInit(): void {
    this.generateBoard();
  }
  trackByIndex(index: number, item: any): number {
    return index;
  }

    
  generateBoard(): void {
    const numRows = 8;
    const numCols = 5;
    const greenCells = 15; // Número de celdas que deben estar en verde

    // Inicializar el tablero con todas las celdas en azul
    this.board = Array(numRows)
      .fill(null)
      .map(() => Array(numCols).fill('#13E5F7'));

    const positions = this.generateRandomPositions(numRows, numCols, greenCells);

    positions.forEach(pos => {
      this.board[pos.row][pos.col] = '#C7C8C8';
    });
  }

  generateRandomPositions(numRows: number, numCols: number, count: number): { row: number, col: number }[] {
    const positions: { row: number, col: number }[] = [];
    const totalCells = numRows * numCols;

    // Generar un conjunto único de posiciones aleatorias
    while (positions.length < count) {
      const randomIndex = Math.floor(Math.random() * totalCells);
      const row = Math.floor(randomIndex / numCols);
      const col = randomIndex % numCols;
      if (!positions.some(pos => pos.row === row && pos.col === col)) {
        positions.push({ row, col });
      }
    }

    return positions;
  }
}
