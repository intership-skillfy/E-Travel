import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OmraPageComponent } from './omra-page.component';

describe('OmraPageComponent', () => {
  let component: OmraPageComponent;
  let fixture: ComponentFixture<OmraPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [OmraPageComponent]
    });
    fixture = TestBed.createComponent(OmraPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
