// offers.module.ts
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { OffersRoutingModule } from './offers-routing.module';
import { OffersComponent } from './offers.component'; 
import { TripsComponent } from './trips/trips.component';
import { HikingComponent } from './hiking/hiking.component';
import { ExcursionsPageComponent } from './excursions-page/excursions-page.component';
import { WidgetsModule } from "../../_metronic/partials/content/widgets/widgets.module";
import { OmraPageComponent } from './omra-page/omra-page.component';
import { OmraModule } from 'src/app/modules/omra/omra.module';

@NgModule({
  declarations: [
    OffersComponent,
    TripsComponent,
    HikingComponent,
    ExcursionsPageComponent,
    OmraPageComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    NgbTooltipModule,
    OffersRoutingModule,
    WidgetsModule,
    OmraModule
  ],
})
export class OffersModule {}
